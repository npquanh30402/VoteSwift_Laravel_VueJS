<?php

namespace App\Http\Controllers\Api;

use App\Enums\BroadcastType;
use App\Events\ResultUpdate;
use App\Events\VotingProcess;
use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\User;
use App\Models\UserJoinTime;
use App\Models\Vote;
use App\Models\VotingRoom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use stdClass;

class VoteController extends Controller
{
    public function getResultPageVotes(VotingRoom $room)
    {
        $votes = $room->votes->sortBy('user_id');

//        $votesWithModels = $votes->map(function ($vote) {
//            return (object)[
//                'user' => $vote->user->only(['id', 'username', 'first_name', 'last_name', 'email', 'avatar']),
//                'question' => $vote->question->decryptQuestion()->only(['id', 'question_title']),
//                'candidate' => $vote->candidate->decryptCandidate()->only(['id', 'candidate_title']),
//            ];
//        })->all();

//        $votesWithModels = $votes->map(function ($vote) {
//            $candidateType = $vote->question->allow_multiple_votes === 1 ? 'checkbox' : 'radio';
//            return (object)[
//                'user' => $vote->user->only(['id', 'username', 'first_name', 'last_name', 'email', 'avatar']),
//                'question' => $vote->question->decryptQuestion()->only(['id', 'question_title']),
//                'candidate' => (object)[
//                    'id' => $vote->candidate->decryptCandidate()->id,
//                    'candidate_title' => $vote->candidate->decryptCandidate()->candidate_title,
//                    'type' => $candidateType,
//                ],
//            ];
//        })->all();

//        $votesWithModels = $room->votes
//            ->sortBy('user_id')
//            ->groupBy('user_id')
//            ->map(function ($votes) {
//                // Assuming all votes for a user have the same candidate type, we can use the first vote to determine the candidate type
//                $firstVote = $votes->first();
//                $candidateType = $firstVote->question->allow_multiple_votes === 1 ? 'checkbox' : 'radio';
//
//                return (object)[
//                    'user' => $firstVote->user->only(['id', 'username', 'first_name', 'last_name', 'email', 'avatar']),
//                    'votes' => $votes->map(function ($vote) use ($candidateType) {
//                        $candidate = $vote->candidate->decryptCandidate();
//                        return [
//                            'question' => $vote->question->decryptQuestion()->only(['id', 'question_title']),
//                            'candidate' => (object)[
//                                'id' => $candidate->id,
//                                'candidate_title' => $candidate->candidate_title,
//                                'type' => $candidateType,
//                            ],
//                        ];
//                    })->all(),
//                ];
//            })->values()->all();

        $votesWithModels = $votes->map(function ($vote) {
            return [
                'id' => $vote->id,
                'name' => $vote->user->username,
                'image' => $vote->user->avatar,
                'price' => 0, // You need to define how to get the price for a vote.
                'category' => "Vote", // Or define a category for votes.
                'rating' => null, // You need to define how to get the rating for a vote.
                'inventoryStatus' => "INSTOCK", // Or define an inventory status for votes.
                'orders' => [
                    [
                        'id' => $vote->id, // Or any unique identifier for the vote.
                        'customer' => $vote->user->first_name . ' ' . $vote->user->last_name,
                        'date' => now()->toDateString(), // Or specify the date as needed.
                        'amount' => 0, // You need to define how to get the amount for a vote.
                        'status' => "PENDING", // Or define a status for votes.
                    ],
                ],
            ];
        })->all();

//        $modifiedVotes = $votes->map(function ($vote) {
//            return (object)[
//                'id' => $vote->id,
//                'name' => "Vote " . $vote->id,
//                'user' => (object)[
//                    'id' => $vote->user_id,
//                    'username' => $vote->user->username,
//                    'first_name' => $vote->user->first_name,
//                    'last_name' => $vote->user->last_name,
//                    'email' => $vote->user->email,
//                    'avatar' => $vote->user->avatar,
//                ],
//                'question' => (object)[
//                    'id' => $vote->question_id,
//                    'question_title' => $vote->question->decryptQuestion()->question_title,
//                ],
//                'candidate' => (object)[
//                    'id' => $vote->candidate_id,
//                    'candidate_title' => $vote->candidate->decryptCandidate()->candidate_title,
//                ]
//            ];
//        })->all();

        $modifiedVotes = $votes->groupBy('user_id')->map(function ($userVotes) {
            $user = $userVotes->first()->user;

            $votes = $userVotes->map(function ($vote) {
                return [
                    'id' => $vote->id,
                    'question_title' => $vote->question->decryptQuestion()->question_title,
                    'candidate_title' => $vote->candidate->decryptCandidate()->candidate_title,
                ];
            })->values()->all();

            return (object)[
                'id' => $user->id,
                'username' => $user->username,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'vote_date' => $userVotes->first()->created_at,
                'votes' => $votes,
            ];
        })->values()->all();

        return response()->json([
            'data' => $modifiedVotes,
            'message' => 'Votes retrieved successfully',
        ]);
    }

    public function getVotes(VotingRoom $room)
    {
        $votes = $room->votes;

        return response()->json([
            'data' => $votes,
            'message' => 'Votes retrieved successfully',
        ]);
    }

    public function storeVotes(VotingRoom $room, Request $request)
    {
        $selectedOptions = json_decode($request->selectedOptions, true);

        foreach ($selectedOptions as $questionId => $candidateIds) {
            $question = Question::findOrFail($questionId);

            if ($question->allow_multiple_votes === false && count($candidateIds) > 1) {
                return response()->json([
                    'message' => 'You can only vote for one candidate at a time.',
                ]);
            }

            if (!empty($candidateIds)) {
                foreach ($candidateIds as $candidateId) {
                    if ($candidateId !== -1) {
                        $candidate = $question->candidates()->findOrFail($candidateId);
                        $this->createVote($room, $question, $candidate);
                    }
                }
            }
        }

        $nestedResults = Vote::getQuestionResults($room->questions);
        $voteCountsInTimeInterval = Vote::calculateVoteCountsInTimeInterval($room);

        broadcast(new ResultUpdate($nestedResults, $voteCountsInTimeInterval));

        return response()->json([
            'message' => 'Your vote has been submitted successfully.',
        ]);
    }

    private function createVote($room, $question, $candidate)
    {
        $vote = new Vote();
        $vote->voting_room_id = $room->id;
        $vote->question_id = $question->id;
        $vote->candidate_id = $candidate->id;
        $vote->user_id = auth()->user()->id;

        $vote->save();
    }

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

        broadcast(new VotingProcess(user: Auth::user(), room: $room, broadcast_type: BroadcastType::VOTING_JOIN));

        return response()->json($userJoinTime);
    }

    public function storeLeaveTime(Request $request, VotingRoom $room)
    {
        $joinTime = UserJoinTime::find($request->joinTimeId);
        $joinTime->leave_time = $request->leave_time;

        $joinTime->save();

        broadcast(new VotingProcess(user: Auth::user(), room: $room, broadcast_type: BroadcastType::VOTING_LEAVE));

        return response()->json(['message' => 'Leave time stored successfully']);
    }

    public function broadcastChoice(VotingRoom $room, Request $request)
    {
        broadcast(new VotingProcess(user: Auth::user(), room: $room, question_id: $request->questionId, candidate_id: $request->candidateId, broadcast_type: BroadcastType::VOTING_CHOICES));
    }

    public function startVote(VotingRoom $room)
    {
        try {
            $user = Auth::user();
            if ($user->id !== $room->user_id) {
                abort(403, 'You are not authorized to start the vote.');
            }

            $room->startVote();

            broadcast(new VotingProcess(user: $user, room: $room, broadcast_type: BroadcastType::VOTING_START))->toOthers();

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
                ->rightJoin('candidates', 'votes.candidate_id', '=', 'candidates.id')
                ->where('candidates.question_id', $question->id)
                ->select('candidates.id', 'candidates.candidate_title', DB::raw('COUNT(votes.id) as vote_count'))
                ->groupBy('candidates.id', 'candidates.candidate_title')
                ->get();

            $candidates = $results->map(function ($result) {
                $result->candidate_title = Crypt::decryptString($result->candidate_title);
                return $result;
            });

            $allCandidates = $question->candidates()->pluck('id');
            $candidatesIdsWithVoteCounts = $candidates->pluck('id');
            $candidatesWithoutVoteCounts = $allCandidates->diff($candidatesIdsWithVoteCounts);
            foreach ($candidatesWithoutVoteCounts as $candidateId) {
                $candidates->push((object)[
                    'id' => $candidateId,
                    'vote_count' => 0,
                ]);
            }

            $vote_results[$question->id] = $candidates;
        }

        return response()->json(compact('vote_results'));
    }
}
