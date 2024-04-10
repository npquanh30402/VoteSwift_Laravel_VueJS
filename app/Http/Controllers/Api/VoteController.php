<?php

namespace App\Http\Controllers\Api;

use App\Events\VotingChoice;
use App\Events\VotingProcess;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserJoinTime;
use App\Models\VotingRoom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    public function deleteUserChoices(VotingRoom $room, User $user)
    {
        Cache::forget('room_' . $room->id . '_user_choices_' . $user->id);

        return response()->json(['message' => 'User choices deleted successfully']);
    }

    public function getUserChoices(VotingRoom $room)
    {
        $allUserChoices = [];

        $userIds = UserJoinTime::where('room_id', $room->id)->pluck('user_id')->unique()->toArray();

        foreach ($userIds as $userId) {
            $serializedChoices = Cache::get('room_' . $room->id . '_user_choices_' . $userId);

            if ($serializedChoices) {
                $choices = json_decode($serializedChoices, true);

                $allUserChoices[$userId] = $choices;
            }
        }

        return response()->json($allUserChoices);
    }

    public function storeUserChoices(Request $request, VotingRoom $room)
    {
        $choices = $request->all();

        $serializedChoices = json_encode($choices);

        Cache::put('room_' . $room->id . '_user_choices_' . Auth::user()->id, $serializedChoices, now()->addHours(24));

        return response()->json(['message' => 'User choices stored successfully']);
    }

    public function deleteJoinTime(VotingRoom $room, User $user)
    {
        return response()->json(['message' => 'Join time removed successfully']);
    }

    public function getJoinTimes(VotingRoom $room)
    {
        $joinTimes = Cache::get('room_' . $room->id . '_user_join_times');
        return response()->json($joinTimes);
    }

    public function storeJoinTime(Request $request, VotingRoom $room)
    {
        $userJoinTime = new UserJoinTime();
        $userJoinTime->user_id = Auth::user()->id;
        $userJoinTime->room_id = $room->id;
        $userJoinTime->join_time = $request->join_time;
        $userJoinTime->save();

        return response()->json(['message' => 'Join time stored successfully']);
    }

    public function broadcastChoice(VotingRoom $room, Request $request)
    {
        broadcast(new VotingChoice(Auth::user(), $room, $request->questionId, $request->candidateId));
    }

    public function startVote(VotingRoom $room)
    {
        try {
            $user = Auth::user();
            if ($user->id !== $room->user_id) {
                abort(403, 'You are not authorized to start the vote.');
            }

            $room->startVote();

            broadcast(new VotingProcess($user, $room))->toOthers();

            return response()->json(['message' => 'Vote started successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
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
