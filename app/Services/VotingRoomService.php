<?php

namespace App\Services;

use App\Http\Requests\VotingRoomRequest;
use App\Models\VotingRoom;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class VotingRoomService
{
    /**
     * @throws Exception
     */
    public function getUserRooms()
    {
        try {
            $user = Auth::user();
            if ($user) {
                return $user->rooms()->get()->transform(function ($room) {
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
                $room->start_time = Carbon::parse($request->start_time)->setTimezone($request->activeTz);
            }

//            dd($request->start_time, Carbon::parse($request->start_time)->setTimezone($request->activeTz), Carbon::parse($request->start_time));
//
//            $startTime = '2024-04-20 08:00:00';
//            dd(Carbon::parse($startTime)->setTimezone('UTC'));

            if (isset($request->end_time)) {
                $room->end_time = Carbon::parse($request->end_time)->setTimezone($request->activeTz);
            }

            $room->save();

            return $room;
        } catch (Exception $e) {
            Log::debug('Error updating question: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function storeVotingRoom(VotingRoomRequest $request): VotingRoom
    {
        try {
            $room = new VotingRoom([
                'room_name' => HelperService::encryptAndStripTags($request->room_name),
                'room_description' => HelperService::encryptAndStripTags($request->room_description),
                'user_id' => Auth::user()->id,
            ]);

            $room->save();

            $room->settings()->create();

            return $room;
        } catch (Exception $e) {
            Log::debug('Error creating question: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteVotingRoom(VotingRoom $room): void
    {
        try {
            $room->delete();
        } catch (Exception $e) {
            Log::debug('Error deleting question: ' . $e->getMessage());
            throw $e;
        }
    }
}
