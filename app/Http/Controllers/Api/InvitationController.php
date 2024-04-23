<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportInvitationsRequest;
use App\Models\Invitation;
use App\Models\User;
use App\Models\VotingRoom;
use App\Notifications\InvitationNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenSpout\Reader\CSV\Reader;
use RuntimeException;

class InvitationController extends Controller
{
    public static function sendInvitation(VotingRoom $room)
    {
        try {
            $authUser = Auth::user();
            $invitedUserIds = $room->invitations()->pluck('invited_user_id');

            $startTime = Carbon::parse($room->start_time);
            $endTime = Carbon::parse($room->end_time);
            $durationUntilEnd = $endTime->diffInSeconds(now());

            foreach ($invitedUserIds as $userId) {
                $user = User::find($userId);

                if ($user) {
                    $token = Str::random(64);

                    Cache::put("ballot.tkn.{$token}", $user->id, $durationUntilEnd);

                    $invitationUrl = route('vote.main', ['token' => $token, 'room' => $room]);

                    $user->notify(new InvitationNotification($room, $authUser, $user, $invitationUrl));
                }
            }

            return response()->json([
                'message' => 'Invitations sent successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function importInvitationsFromCSV(VotingRoom $room, ImportInvitationsRequest $request)
    {
        $this->authorize('create', $room);

        DB::beginTransaction();
        try {
            if (!$request->hasFile('csv_file')) {
                throw new RuntimeException('File not found');
            }

            $file = $request->file('csv_file');

            $reader = new Reader();
            $reader->open($file->getPathname());

            $data = [];

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $rowData = [];
                    foreach ($row->getCells() as $cell) {
                        $rowData[] = $cell->getValue();
                    }
                    $data[] = $rowData;
                }
            }

            $invitedUsers = [];

            foreach ($data as $iValue) {
                $user = User::find($iValue[0]);

                if (!$user) {
                    $user = User::where('username', $iValue[1])->first();
                }

                if (!$user) {
                    $user = User::where('email', $iValue[2])->first();
                }

                if (!$user) {
                    return response()->json(['message' => 'User with ID, username, or email ' . $iValue[0] . ', ' . $iValue[1] . ', ' . $iValue[2] . ' does not exist.'], 404);
                }

                if ($user->id === $room->user_id) {
                    continue;
                }

                Invitation::updateOrCreate([
                    'voting_room_id' => $room->id,
                    'invited_user_id' => $user->id,
                ]);

                $invitedUsers[] = $user;
            }

            $reader->close();

            unlink($file->getPathname());

            DB::commit();

            return response()->json([
                'data' => $invitedUsers,
                'message' => 'Invitations imported successfully.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function index(VotingRoom $room)
    {
        try {
            $invitations = $room->invitations;

            $invitedUsers = [];

            foreach ($invitations as $invitation) {
                $invitedUser = User::find($invitation->invited_user_id);
                $invitedUsers[] = $invitedUser;
            }

            return response()->json([
                'data' => $invitedUsers,
                'message' => 'Invitations fetched successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(VotingRoom $room, Request $request)
    {
        $this->authorize('create', $room);

        DB::beginTransaction();
        try {
            $existingInvitations = Invitation::where('voting_room_id', $room->id)
                ->pluck('invited_user_id')
                ->toArray();

            if (empty($request->user_ids)) {
                Invitation::where('voting_room_id', $room->id)->delete();
            } else {
                $invitedUserIdsToDelete = array_diff($existingInvitations, array_column($request->user_ids, 'id'));

                Invitation::where('voting_room_id', $room->id)
                    ->whereIn('invited_user_id', $invitedUserIdsToDelete)
                    ->delete();

                foreach ($request->user_ids as $invitationData) {
                    if ($invitationData['id'] == auth()->user()->id) {
                        continue;
                    }

                    if (Invitation::where('voting_room_id', $room->id)
                        ->where('invited_user_id', $invitationData['id'])
                        ->exists()) {
                        continue;
                    }

                    Invitation::create([
                        'voting_room_id' => $room->id,
                        'invited_user_id' => $invitationData['id'] ?? null,
                    ]);
                }
            }

            $invitations = $room->invitations;

            $invitedUsers = [];

            foreach ($invitations as $invitation) {
                $invitedUser = User::find($invitation->invited_user_id);
                $invitedUsers[] = $invitedUser;
            }

            DB::commit();

            return response()->json([
                'data' => $invitedUsers,
                'message' => 'Invitations updated successfully'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
