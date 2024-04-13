<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\VotingRoom;
use App\Notifications\RoomPublish;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;

class VotingRoomController extends Controller
{
    public function publishRoom(VotingRoom $room)
    {
        if ($room->is_published) {
            return back()->with('error', 'Voting room is already published!');
        }

        if ($room->questions()->count() < 1) {
            return back()->with('error', 'Voting room must have at least 1 question!');
        }

        if ($room->start_time == null || $room->end_time == null) {
            return back()->with('error', 'Voting room must have start and end date!');
        }

        $room->is_published = true;

        $room->save();

        $room->user->notify(new RoomPublish($room));

        InvitationController::sendInvitation($room);

        return back()->with('success', 'Voting room published successfully!');
    }

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
