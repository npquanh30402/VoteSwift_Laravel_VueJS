<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\VotingRoom;
use App\Services\CandidateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;

class CandidateController extends Controller
{
    protected $candidateService;

    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
    }

    public function QuestionCandidates(Question $question)
    {
        $candidates = $question->candidates()->get();

        $candidates->each(function ($candidate) {
            $candidate->decryptCandidate();
        });

        return response()->json($candidates);
    }

    public function RoomCandidates(VotingRoom $room)
    {
        $questions = $room->questions;

        $candidates = [];

        foreach ($questions as $question) {
            $questionCandidates = $question->candidates()->get();

            $questionCandidates->each(function ($candidate) {
                $candidate->decryptCandidate();
            });

            $candidates[$question->id] = $questionCandidates;
        }

        return response()->json($candidates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
