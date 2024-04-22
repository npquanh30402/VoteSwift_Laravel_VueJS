<?php

namespace App\Http\Controllers\Api;

use App\Enums\BroadcastType;
use App\Events\ResultUpdate;
use App\Events\VotingProcess;
use App\Events\VotingResultEvent;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Question;
use App\Models\User;
use App\Models\UserJoinTime;
use App\Models\Vote;
use App\Models\VotingRoom;
use App\Services\HelperService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use stdClass;

class VoteController extends Controller
{
    public function handleTieAndCreateNewVotingRound(VotingRoom $room)
    {
        DB::beginTransaction();
        try {
            $winningOptions = Vote::getWinningOptions($room);
            $winningQuestions = [];
            $winningCandidates = [];

            $keysWithMultipleCandidates = array_filter(array_keys($winningOptions), function ($key) use ($winningOptions) {
                return count($winningOptions[$key]) >= 2;
            });

            foreach ($keysWithMultipleCandidates as $key) {
                $question = Question::findOrFail($key);
                $winningQuestions[] = $question;

                foreach ($winningOptions[$key] as $candidateId) {
                    $candidate = Candidate::findOrFail($candidateId);
                    $winningCandidates[] = $candidate;
                }
            }

            $original = $room->decryptVotingRoom();
            $new_room_name = $this->incrementRound($original->room_name);

            $copy = $original->replicate()->fill([
                'room_name' => HelperService::encryptAndStripTags($new_room_name),
                'room_description' => HelperService::encryptAndStripTags($original->room_description),
                'start_time' => null,
                'end_time' => null,
                'is_published' => 0,
                'vote_started' => 0,
                'has_ended' => 0
            ]);

            $copy->save();

            if ($original->settings) {
                $newSettings = $original->settings->replicate()->fill([
                    'password' => null,
                    'password_qrcode' => null,
                ]);
                $newSettings->voting_room_id = $copy->id;
                $newSettings->save();
            }

            if ($original->invitationMail) {
                $oldMail = $original->invitationMail->decryptInvitationMail();
                $newInvitationMail = $oldMail->replicate()->fill([
                    'mail_subject' => HelperService::encryptAndStripTags($oldMail->mail_subject),
                    'mail_content' => HelperService::encryptAndStripTags($oldMail->mail_content)
                ]);
                $newInvitationMail->voting_room_id = $copy->id;
                $newInvitationMail->save();
            }

            foreach ($winningQuestions as $question) {
                $oldQuestion = $question->decryptQuestion();

                $newQuestion = $oldQuestion->replicate()->fill([
                    'question_title' => HelperService::encryptAndStripTags($oldQuestion->question_title),
                    'question_description' => HelperService::encryptAndStripTags($oldQuestion->question_description),
                    'question_image' => $oldQuestion->question_image
                ]);
                $newQuestion->voting_room_id = $copy->id;
                $newQuestion->save();

                foreach ($winningCandidates as $candidate) {
                    if ($candidate->question_id !== $question->id) {
                        continue;
                    }
                    
                    $oldCandidate = $candidate->decryptCandidate();

                    $newCandidate = $oldCandidate->replicate()->fill([
                        'candidate_title' => HelperService::encryptAndStripTags($oldCandidate->candidate_title),
                        'candidate_description' => HelperService::encryptAndStripTags($oldCandidate->candidate_description),
                        'candidate_image' => $oldCandidate->candidate_image,
                    ]);

                    $newCandidate->question_id = $newQuestion->id;
                    $newCandidate->save();
                }
            }

            foreach ($original->attachments as $attachment) {
                $newAttachment = $attachment->replicate();
                $newAttachment->voting_room_id = $copy->id;

                $newAttachment->save();
            }

            foreach ($original->invitations as $invitation) {
                $newInvitation = $invitation->replicate();
                $newInvitation->voting_room_id = $copy->id;
                $newInvitation->save();
            }

            DB::commit();

            return response()->json([
                'data' => $copy->decryptVotingRoom(),
                'message' => 'Voting room duplicated successfully!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    function incrementRound($room_name)
    {
        if (preg_match('/\[Round (\d+)\]/', $room_name, $matches)) {
            $current_round = (int)$matches[1];
            return str_replace("[Round $current_round]", "[Round " . ($current_round + 1) . "]", $room_name);
        }

        return "[Round 2] $room_name";
    }

    public function getResultPageVotes(VotingRoom $room)
    {
        $votes = $room->votes->sortBy('user_id');

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
        try {
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
                            $vote = $this->createVote($room, $question, $candidate);

                            broadcast(new VotingResultEvent($room, $vote));
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
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function createVote($room, $question, $candidate)
    {
        DB::beginTransaction();
        try {
            $vote = new Vote();
            $vote->voting_room_id = $room->id;
            $vote->question_id = $question->id;
            $vote->candidate_id = $candidate->id;
            $vote->user_id = auth()->user()->id;

            $vote->save();

            DB::commit();

            return $vote;
        } catch (Exception $e) {
            DB::rollBack();
            throw new RuntimeException($e->getMessage());
        }
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
