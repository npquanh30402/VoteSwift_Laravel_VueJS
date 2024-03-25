<?php

namespace App\Http\Controllers;

use App\Events\ResultUpdate;
use App\Models\Candidate;
use App\Models\Question;
use App\Models\User;
use App\Models\Vote;
use App\Models\VotingRoom;
use App\Services\VoteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
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

//        dd($votingHistory, $organizedData, $room_info);

        return Inertia::render('Users/VotingHistory', compact('votingHistory', 'organizedData', 'room_info'));
    }

    public function result(VotingRoom $room)
    {
        $room->room_name = Crypt::decryptString($room->room_name);
        $questions = $room->questions;

        $nestedResults = Vote::getQuestionResults($questions);

        $user_has_voted_ids = Vote::collectUserVoteIds($questions, $room);

        $user = Auth::user();
        $user_choices = [];

        foreach ($questions as $question) {
            $userVotes = [];

            if ($user) {
                $votes = Vote::getUserVotes($user, $question);

                foreach ($votes as $vote) {
                    $candidateTitle = Crypt::decryptString($vote->candidate_title);
                    $candidate = Candidate::find($vote->candidate_id);

                    if ($candidate) {
                        $candidate->candidate_title = $candidateTitle;
                        $userVotes[] = $candidate;
                    }
                }
            }

            $user_choices[] = [
                'question' => $question,
                'user_vote' => $userVotes,
            ];
        }

        $user_choices = collect($user_choices);

//        dd($room, $nestedResults, $user_has_voted_ids, $user_choices);

        return Inertia::render('Voting/Vote/VotingResult', compact('room', 'nestedResults', 'user_has_voted_ids', 'user_choices'));
    }


    public function passwordForm(VotingRoom $room)
    {
        return Inertia::render('Voting/PasswordEntry', compact('room'));
    }

    public function passwordEntry(Request $request, VotingRoom $room)
    {
        $user = Auth::user();

        $password = $request->room_password;

        if (Hash::check($password, $room->settings->password)) {

            $session_name = "{$user->id}_voting_room_password_{$room->id}";

            $request->session()->put($session_name, true);
            return redirect()->route('vote.main', ['room' => $room]);
        }

        return back()->with('error', 'Incorrect password.');
    }

    public function main(VotingRoom $room, Request $request)
    {
        $user = Auth::user();

//        if (!$room->settings->allow_voting) {
//            return redirect()->route('homepage')->with('error', 'Voting is currently disabled.');
//        }
//
//        $hasVoted = $user->hasVotedForVotingRoom($room->id);
//
//        if ($hasVoted) {
//            $request->session()->now('error', 'You have already voted for this voting room.');
//        }
//
//        $now = Carbon::now()->setTimezone($room->timezone);
//        if ($room->start_time < $now) {
//            $request->session()->now('error', 'Voting has not started.');
//        }
//
//        $isResultHidden = $room->settings->results_visibility === 'after_voting' || $user->id === $room->user_id;
//
//        $endTime = Carbon::parse($room->end_time, $room->timezone);
//        $hasEnded = $endTime->isPast();

        $questions = $room->questions()->with('candidates')->get()->map(function ($question) {
            $question->question_title = Crypt::decryptString($question->question_title);
            $question->question_description = Crypt::decryptString($question->question_description);
            $question->candidates = $question->candidates->map(function ($candidate) {
                $candidate->candidate_title = Crypt::decryptString($candidate->candidate_title);
                $candidate->candidate_description = Crypt::decryptString($candidate->candidate_description);
                return $candidate;
            });
            return $question;
        });

//        $isMultipleChoice = $room->settings->allow_multiple_votes;

//        return Inertia::render('Voting/VotePage', compact('room', 'questions', 'isMultipleChoice', 'hasVoted', 'isResultHidden', 'hasEnded'));
//        dd($room);
        $room->room_name = Crypt::decryptString($room->room_name);
        $room->room_description = Crypt::decryptString($room->room_description);

        return Inertia::render('Voting/Vote/Index', compact('questions', 'room'));
    }

    public function store(VotingRoom $room, Request $request)
    {
        $selectedOptions = $request->selectedOptions;

        foreach ($selectedOptions as $questionId => $candidateIds) {
            if (!empty($candidateIds)) {
                foreach ($candidateIds as $candidateId) {
                    $this->createVote($candidateId);
                }
            }
        }

        $nestedResults = Vote::getQuestionResults($room->questions);

        broadcast(new ResultUpdate($nestedResults));

        return redirect()->route('homepage')->with('success', 'Thank you for voting!');
    }

    private function createVote($candidateId)
    {
        $vote = new Vote();
        $vote->candidate_id = $candidateId;
        $vote->user_id = auth()->user()->id;

        $vote->save();
    }
}
