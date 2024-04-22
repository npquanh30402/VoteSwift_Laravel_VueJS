<?php

namespace App\Services;

use App\Http\Controllers\InvitationController;
use App\Http\Requests\VotingRoomRequest;
use App\Models\User;
use App\Models\VotingRoom;
use App\Notifications\RoomPublish;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class VotingRoomService
{
    /**
     * @throws Exception
     */
    public function fetchRoom(VotingRoom $room)
    {
        try {
            return $room->decryptVotingRoom();
        } catch (Exception $e) {
            Log::debug('Error fetching room: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function getUserRooms()
    {
        try {
            $user = Auth::user();
            if ($user) {
                return $user->rooms()->get()->transform(function ($room) {
                    if (Carbon::now()->gt(Carbon::parse($room->end_time)))
                        $room->endVote();

                    return $room->decryptVotingRoom();
                });
            }

            throw new RuntimeException('User is null');
        } catch (Exception $e) {
            Log::debug('Error getting user rooms: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function updateVotingRoom(Request $request, VotingRoom $room): ?VotingRoom
    {
        DB::beginTransaction();
        try {
            if (isset($request->room_name)) {
                $room->room_name = HelperService::encryptAndStripTags($request->room_name);
            }

            if (isset($request->room_description)) {
                $room->room_description = HelperService::encryptAndStripTags($request->room_description);
            }

            if (isset($request->activeTz)) {
                $room->timezone = $request->activeTz;
            }

            if (isset($request->start_time)) {
                $room->start_time = Carbon::parse($request->start_time);
            }

            if (isset($request->end_time)) {
                $room->end_time = Carbon::parse($request->end_time);
            }

            if (isset($request->end_time)) {
                $room->end_time = Carbon::parse($request->end_time);
            }

            if (isset($request->is_published)) {
                $this->publishRoom($room);
            }

            $room->save();

            DB::commit();

            return $room;
        } catch (Exception $e) {
            DB::rollBack();
            Log::debug('Error updating question: ' . $e->getMessage());
            throw $e;
        }
    }

    private function publishRoom(VotingRoom $room)
    {
        if ($room->is_published) {
            throw new RuntimeException('Voting room is already published!');
        }

        if ($room->questions()->count() < 1) {
            throw new RuntimeException('Voting room must have at least 1 question!');
        }

        if ($room->start_time == null || $room->end_time == null) {
            throw new RuntimeException('Voting room must have start and end date!');
        }

        $room->is_published = 1;
        $room->user->notify(new RoomPublish($room));

        if ($room->settings->invitation_only === 1) {
            InvitationController::sendInvitation($room);
        }
    }

    /**
     * @throws Exception
     */
    public function storeVotingRoom(VotingRoomRequest $request): VotingRoom
    {
        DB::beginTransaction();
        try {
            $room = new VotingRoom([
                'room_name' => HelperService::encryptAndStripTags($request->room_name),
                'room_description' => HelperService::encryptAndStripTags($request->room_description),
                'user_id' => Auth::user()->id,
            ]);

            $room->save();

            DB::commit();

            $room->settings()->create();

            return VotingRoom::findOrFail($room->id);
        } catch (Exception $e) {
            DB::rollBack();
            Log::debug('Error creating question: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteVotingRoom(VotingRoom $room): void
    {
        DB::beginTransaction();
        try {
            $room->delete();

            DB::rollBack();
        } catch (Exception $e) {
            DB::commit();
            Log::debug('Error deleting question: ' . $e->getMessage());
            throw $e;
        }
    }
}
