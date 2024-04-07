<?php

namespace App\Http\Controllers\Api;

use App\Events\VotingChoice;
use App\Events\VotingProcess;
use App\Http\Controllers\Controller;
use App\Models\Vote;
use App\Models\VotingRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    public function broadcastChoice(VotingRoom $room, Request $request)
    {
        broadcast(new VotingChoice(Auth::user(), $room, $request->questionId, $request->candidateId));
    }

    public function startVote(VotingRoom $room)
    {
        if (Auth::user()->id !== $room->user_id) {
            abort(403, 'You are not authorized to start the vote.');
        }

        $room->startVote();

        broadcast(new VotingProcess($room));

        return response()->json(['message' => 'Vote started successfully']);
    }

    public function getVoteResults(VotingRoom $room)
    {
        $questions = $room->questions;
        $vote_results = [];

        foreach ($questions as $question) {
            $results = DB::table('votes')
                ->join('candidates', 'votes.candidate_id', '=', 'candidates.id')
                ->where('candidates.question_id', $question->id)
                ->select('candidates.id', 'candidates.candidate_title', DB::raw('COUNT(*) as vote_count'))
                ->groupBy('candidates.id', 'candidates.candidate_title')
                ->get();

            $candidates = $results->map(function ($result) {
                $result->candidate_title = Crypt::decryptString($result->candidate_title);
                return $result;
            });

            $vote_results[$question->id] = $candidates;
        }

//        $vote_results = Vote::getQuestionResults($room->questions);

        return response()->json(compact('vote_results'));
    }
}
