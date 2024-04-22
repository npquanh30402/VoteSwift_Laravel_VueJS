<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VotingRoom;
use Exception;
use Illuminate\Http\Request;

class UserJoinTimeController extends Controller
{
    public function index(VotingRoom $room)
    {
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

        try {
            return response()->json([
                'data' => $modifiedJoinTimes,
                'message' => 'User join times retrieved successfully',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
