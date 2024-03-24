<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Question;
use App\Services\CandidateService;
use Exception;
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

    public function main(Question $question)
    {
        $candidates = $question->candidates->map(function ($candidate) {
            $candidate->candidate_title = Crypt::decryptString($candidate->candidate_title);
            $candidate->candidate_description = Crypt::decryptString($candidate->candidate_description);
            return $candidate;
        });

        $question->question_title = Crypt::decryptString($question->question_title);

        return Inertia::render('Voting/Candidate/Index', compact('question', 'candidates'));

        return view('voting.candidate.main', compact('question', 'candidates'));
    }

    public function store(Question $question, Request $request)
    {
        try {
            $this->candidateService->createCandidate($question, $request);
            return back()->with('success', 'Candidate added successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Error adding candidate: ' . $e->getMessage());
        }
    }

    public function delete(Candidate $candidate)
    {
        try {
            $this->candidateService->deleteCandidate($candidate);
            return back()->with('success', 'Candidate deleted successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Error deleting candidate: ' . $e->getMessage());
        }
    }

    public function update(Candidate $candidate, Request $request)
    {
        try {
            $this->candidateService->updateCandidate($candidate, $request);
            return back()->with('success', 'Candidate updated successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Error updating candidate: ' . $e->getMessage());
        }
    }
}
