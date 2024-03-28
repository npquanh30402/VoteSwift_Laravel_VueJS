<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Question;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CandidateService
{
    public function createCandidate(Question $question, Request $request): void
    {
        try {
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
        } catch (Exception $e) {
            Log::debug("An error occurred while creating the candidate: " . $e->getMessage());
        }
    }

    public function updateCandidate(Candidate $candidate, Request $request): void
    {
        try {
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
        } catch (Exception $e) {
            Log::debug("An error occurred while updating the candidate: " . $e->getMessage());
        }
    }

    public function deleteCandidate(Candidate $candidate): void
    {
        try {
            $candidate->delete();
        } catch (Exception $e) {
            Log::debug("An error occurred while deleting the candidate: " . $e->getMessage());
        }
    }
}
