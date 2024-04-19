<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportInvitationsRequest;
use App\Models\Invitation;
use App\Models\User;
use App\Models\VotingRoom;
use Illuminate\Http\Request;
use OpenSpout\Reader\CSV\Reader;

class InvitationController extends Controller
{
    public function importInvitationsFromCSV(VotingRoom $room, ImportInvitationsRequest $request)
    {
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');

            $reader = new Reader();
            $reader->open($file->getPathname());

            $data = [];

            foreach ($reader->getSheetIterator() as $sheet) {
                $firstRow = true;
                foreach ($sheet->getRowIterator() as $row) {
                    if ($firstRow) {
                        $firstRow = false;
                        continue;
                    }
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

                Invitation::updateOrCreate([
                    'voting_room_id' => $room->id,
                    'invited_user_id' => $user->id,
                ]);

                $invitedUsers[] = $user;
            }

            $reader->close();

            unlink($file->getPathname());

            return response()->json([
                'data' => $invitedUsers,
                'message' => 'Invitations imported successfully.'
            ]);
        }

        return response()->json(['message' => 'No CSV file uploaded.'], 400);
    }

    public function getInvitations(VotingRoom $room)
    {
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
    }

    public function store(VotingRoom $room, Request $request)
    {
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

        return response()->json([
            'data' => $invitedUsers,
            'message' => 'Invitations updated successfully'
        ]);
    }
}
