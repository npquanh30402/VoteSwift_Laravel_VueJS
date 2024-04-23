<?php

namespace App\Http\Controllers\Api;

use App\Enums\BroadcastType;
use App\Events\VotingProcess;
use App\Http\Controllers\Controller;
use App\Models\UserAttendance;
use App\Models\VotingRoom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserAttendanceController extends Controller
{
    public function storeJoinTime(Request $request, VotingRoom $room)
    {
        DB::beginTransaction();
        try {
            $joinTime = new UserAttendance();
            $joinTime->user_id = Auth::user()->id;
            $joinTime->room_id = $room->id;
            $joinTime->join_time = $request->join_time;
            $joinTime->save();

            DB::commit();

            broadcast(new VotingProcess(user: Auth::user(), room: $room, broadcast_type: BroadcastType::VOTING_JOIN));

            return response()->json([
                'data' => $joinTime,
                'message' => 'Join time stored successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function storeLeaveTime(Request $request, VotingRoom $room)
    {
        DB::beginTransaction();
        try {
            $joinTime = UserAttendance::find($request->joinTimeId);
            $joinTime->leave_time = $request->leave_time;

            $joinTime->save();

            DB::commit();

            broadcast(new VotingProcess(user: Auth::user(), room: $room, broadcast_type: BroadcastType::VOTING_LEAVE));

            return response()->json([
                'data' => $joinTime,
                'message' => 'Leave time stored successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function index(VotingRoom $room)
    {
        try {
            $joinTimes = $room->userJoinTimes->sortBy('user_id');

            $modifiedJoinTimes = $joinTimes->groupBy('user_id')->map(function ($data) {
                $user = $data->first()->user;

                $joins = $data->map(function ($join) {
                    return [
                        'id' => $join->id,
                        'join_time' => $join->join_time,
                        'leave_time' => $join->leave_time
                    ];
                })->values()->all();

                return (object)[
                    'id' => $user->id,
                    'username' => $user->username,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'joins' => $joins,
                ];
            })->values()->all();

            return response()->json([
                'data' => $modifiedJoinTimes,
                'message' => 'User join times retrieved successfully',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
