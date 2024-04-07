<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VotingRoomRequest;
use App\Models\VotingRoom;
use App\Notifications\RoomCreation;
use App\Services\HelperService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class VotingRoomController extends Controller
{
    public function index()
    {
        $rooms = auth()->user()->rooms()->get()->transform(function ($room) {
            return $room->decryptVotingRoom();
        });

        return response()->json($rooms);
    }

    public function delete(VotingRoom $room)
    {
        $room->delete();

        return response()->json(null, 204);
    }

    public function store(VotingRoomRequest $request)
    {
        $room = new VotingRoom([
            'room_name' => HelperService::encryptAndStripTags($request->room_name),
            'room_description' => HelperService::encryptAndStripTags($request->room_description),
            'user_id' => auth()->user()->id,
        ]);

        $room->save();

        $room->settings()->create();

        $room->user->notify(new RoomCreation($room));

        return response()->json($room->decryptVotingRoom(), 201);
    }
}
