<?php

namespace App\Http\Controllers;

use App\Events\ResultUpdate;
use App\Events\VotingProcess;
use App\Models\Candidate;
use App\Models\Question;
use App\Models\User;
use App\Models\UserJoinTime;
use App\Models\Vote;
use App\Models\VotingRoom;
use App\Services\VoteService;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

        $this->middleware('voting_room_password')->only(['main']);
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

        $voteCountsInTimeInterval = Vote::calculateVoteCountsInTimeInterval($room);

        return Inertia::render('Voting/Vote/VotingResult', compact('room', 'nestedResults', 'user_has_voted_ids', 'user_choices', 'voteCountsInTimeInterval'));
    }


    public function passwordForm(VotingRoom $room)
    {
        return Inertia::render('Voting/Vote/PasswordEntry', compact('room'));
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

    public function main(VotingRoom $room)
    {
//        $this->authorize('joinInvitation', [$room, $request->query('token')]);

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

        $room->decryptVotingRoom();

        return Inertia::render('Voting/Vote/Index', compact('questions', 'room'));
    }

    public function store(VotingRoom $room, Request $request)
    {
        $selectedOptions = $request->selectedOptions;

        foreach ($selectedOptions as $questionId => $candidateIds) {
            $question = Question::findOrFail($questionId);

            if ($question->allow_multiple_votes === false && count($candidateIds) > 1) {
                return back()->with('error', 'The question does not allow multiple votes.');
            }

            if (!empty($candidateIds)) {
                foreach ($candidateIds as $candidateId) {
                    $this->createVote($candidateId);
                }
            }
        }

        $nestedResults = Vote::getQuestionResults($room->questions);
        $voteCountsInTimeInterval = Vote::calculateVoteCountsInTimeInterval($room);

        broadcast(new ResultUpdate($nestedResults, $voteCountsInTimeInterval));

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
