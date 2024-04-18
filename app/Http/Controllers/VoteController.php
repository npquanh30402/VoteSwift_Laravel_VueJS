<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\VotingRoom;
use App\Services\VoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class VoteController extends Controller
{
    protected $voteService;

    public function __construct(VoteService $voteService)
    {
        $this->voteService = $voteService;
    }

    public function userHistory()
    {
        $user = Auth::user();

        $votingHistory = Vote::where('user_id', $user->id)->paginate(10);

        $organizedData = [];
        $room_info = [];

        foreach ($votingHistory as $vote) {
            $candidate = $vote->candidate;
            $question = $candidate->question;
            $room = $question->room;

            $organizedData[] = [
                'room_id' => $room->id,
                'question_title' => Crypt::decryptString($question->question_title),
                'candidate_id' => $candidate->id,
                'candidate_title' => Crypt::decryptString($candidate->candidate_title),
            ];

            $room_info[$room->id] = [
                'room_id' => $room->id,
                'room_title' => Crypt::decryptString($room->room_name),
            ];
        }

        return Inertia::render('Users/VotingHistory', compact('votingHistory', 'organizedData', 'room_info'));
    }

    public function passwordForm(VotingRoom $room, $token = null)
    {
        return Inertia::render('Voting/Vote/PasswordEntry', compact('room', 'token'));
    }

    public function passwordEntry(Request $request, VotingRoom $room)
    {
        $user = Auth::user();
        $password = $request->room_password;

        $token = null;
        if (isset($request->token)) {
            $token = $request->token;
        }

        if (Hash::check($password, $room->settings->password)) {
            $cacheKey = "{$user->id}_voting_room_password_{$room->id}";
            Cache::put($cacheKey, true);

            return redirect()->route('vote.main', ['room' => $room, 'token' => $token]);
        }

        return back()->with('error', 'Incorrect password.');
    }

    public function main(VotingRoom $room)
    {
//        $this->authorize('joinInvitation', [$room, $request->query('token')]);
        $room->decryptVotingRoom();

        $owner = $room->user->only(['id', 'username', 'avatar']);

        return Inertia::render('Voting/Vote/Index', compact('room', 'owner'));
    }
}
