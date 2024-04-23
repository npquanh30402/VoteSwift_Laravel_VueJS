<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\VotingRoom;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;

class VotingRoomController extends Controller
{
    public function showPublicRoom()
    {
        $public_rooms = VotingRoom::getPublicRooms()->paginate(9);

        $public_rooms->each(function ($room) {
            $room->room_name = Crypt::decryptString($room->room_name);
            $room->room_description = Crypt::decryptString($room->room_description);

            return $room;
        });

        return Inertia::render('Voting/PublicRooms', compact('public_rooms'));
    }

    public function dashboard(VotingRoom $room)
    {
        $this->authorize('view', $room);

        $room->decryptVotingRoom();

        $nestedResults = Vote::getQuestionResults($room->questions);
        $voteCountsInTimeInterval = Vote::calculateVoteCountsInTimeInterval($room);

        return Inertia::render('Voting/VotingRoom/Dashboard', compact('room', 'nestedResults', 'voteCountsInTimeInterval'));
    }

    public function create()
    {
        return Inertia::render('Voting/VotingRoom/CreateRoom');
    }
}
