<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Question;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class CandidateService
{
    public function createCandidate(Question $question, Request $request)
    {
        try {
            $candidate = new Candidate();

            $candidate->candidate_title = Crypt::encryptString(strip_tags($request->candidate_title));
            $candidate->candidate_description = Crypt::encryptString(strip_tags($request->candidate_description));
            $candidate->question_id = $question->id;

            $candidate->save();

            return $candidate;
        } catch (Exception $e) {
            Log::debug("An error occurred while creating the candidate: " . $e->getMessage());
        }
    }

    public function updateCandidate(Candidate $candidate, Request $request)
    {
        try {
            $candidate->candidate_title = Crypt::encryptString(strip_tags($request->candidate_title));
            $candidate->candidate_description = Crypt::encryptString(strip_tags($request->candidate_description));

            $candidate->save();

            return $candidate;
        } catch (Exception $e) {
            Log::debug("An error occurred while updating the candidate: " . $e->getMessage());
        }
    }

    public function deleteCandidate(Candidate $candidate)
    {
        try {
            $candidate->delete();

            return $candidate;
        } catch (Exception $e) {
            Log::debug("An error occurred while deleting the candidate: " . $e->getMessage());
        }
    }
}
