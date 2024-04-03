<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Question;
use App\Models\VotingRoom;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
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
    public function store(Question $question, Request $request)
    {
        $candidate = new Candidate();

        $candidate->candidate_title = HelperService::encryptAndStripTags($request->candidate_title);

        $candidate->candidate_description = HelperService::encryptAndStripTags($request->candidate_description);

        $candidate->question_id = $question->id;

        if ($request->hasFile('candidate_image')) {
            $fileName = uniqid('', true) . '.' . $request->candidate_image->getClientOriginalExtension();

            $fileName = HelperService::sanitizeFileName($fileName);

            $request->candidate_image->storeAs('uploads/candidates', $fileName, 'public');
            $candidate->candidate_image = $fileName;
        }

        $candidate->save();

        $candidate->decryptCandidate();

        return response()->json($candidate, 201);
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
    public function update(Candidate $candidate, Request $request)
    {
        $candidate->candidate_title = HelperService::encryptAndStripTags($request->candidate_title);

        $candidate->candidate_description = HelperService::encryptAndStripTags($request->candidate_description);

        $oldImage = $candidate->candidate_image;
        if ($request->hasFile('candidate_image')) {
            $fileName = uniqid('', true) . '.' . $request->candidate_image->getClientOriginalExtension();

            $fileName = HelperService::sanitizeFileName($fileName);

            $request->candidate_image->storeAs('uploads/candidates', $fileName, 'public');
            $candidate->candidate_image = $fileName;
        }

        if ($oldImage !== $candidate->candidate_image) {
            Storage::delete(str_replace('/storage/', 'public/', $oldImage));
        }

        $candidate->save();

        $candidate->decryptCandidate();

        return response()->json($candidate, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidate $candidate)
    {
        $candidate->delete();

        return response()->json();
    }
}
