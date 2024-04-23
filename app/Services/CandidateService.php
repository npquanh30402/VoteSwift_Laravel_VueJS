<?php
//
//namespace App\Services;
//
//use App\Http\Requests\CandidateRequest;
//use App\Models\Candidate;
//use App\Models\Question;
//use App\Models\VotingRoom;
//use Exception;
//use Illuminate\Database\Eloquent\Collection;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Log;
//use Illuminate\Support\Facades\Storage;
//
//class CandidateService
//{
//    /**
//     * @throws Exception
//     */
//    public function getQuestionCandidates(Question $question): Collection
//    {
//        try {
//            $candidates = $question->candidates()->get();
//
//            $candidates->each(function ($candidate) {
//                $candidate->decryptCandidate();
//            });
//
//            return $candidates;
//        } catch (Exception $e) {
//            Log::error("Error getting candidates for question '$question->id':" . $e->getMessage());
//            throw $e;
//        }
//    }
//
//    /**
//     * @throws Exception
//     */
//    public function getRoomCandidates(VotingRoom $room): ?array
//    {
//        try {
//            $questions = $room->questions;
//
//            $candidates = [];
//
//            foreach ($questions as $question) {
//                $questionCandidates = $question->candidates()->get();
//
//                $questionCandidates->each(function ($candidate) {
//                    $candidate->decryptCandidate();
//                });
//
//                $candidates[$question->id] = $questionCandidates;
//            }
//
//            return $candidates;
//        } catch (Exception $e) {
//            Log::error("Error getting candidates for room '$room->id':" . $e->getMessage());
//            throw $e;
//        }
//    }
//
//    /**
//     * @throws Exception
//     */
//    public function storeCandidate(Question $question, CandidateRequest $request): Candidate
//    {
//        try {
//            $candidate = new Candidate();
//
//            $candidate->candidate_title = HelperService::encryptAndStripTags($request->candidate_title);
//
//            if ($request->candidate_description) {
//                $candidate->candidate_description = HelperService::encryptAndStripTags($request->candidate_description);
//            }
//
//            $candidate->question_id = $question->id;
//
//            if ($request->hasFile('candidate_image')) {
//                $fileName = uniqid('', true) . '.' . $request->candidate_image->getClientOriginalExtension();
//
//                $fileName = HelperService::sanitizeFileName($fileName);
//
//                $request->candidate_image->storeAs('uploads/candidates', $fileName, 'public');
//                $candidate->candidate_image = $fileName;
//            }
//
//            $candidate->save();
//
//            return $candidate;
//        } catch (Exception $e) {
//            Log::error('Error creating candidate: ' . $e->getMessage());
//            throw $e;
//        }
//    }
//
//    /**
//     * @throws Exception
//     */
//    public function updateCandidate(Candidate $candidate, Request $request): Candidate
//    {
//        try {
//            $candidate->candidate_title = HelperService::encryptAndStripTags($request->candidate_title);
//
//            $candidate->candidate_description = HelperService::encryptAndStripTags($request->candidate_description);
//
//            $oldImage = $candidate->candidate_image;
//            if ($request->hasFile('candidate_image')) {
//                $fileName = uniqid('', true) . '.' . $request->candidate_image->getClientOriginalExtension();
//
//                $fileName = HelperService::sanitizeFileName($fileName);
//
//                $request->candidate_image->storeAs('uploads/candidates', $fileName, 'public');
//                $candidate->candidate_image = $fileName;
//            }
//
//            if ($oldImage !== $candidate->candidate_image) {
//                Storage::delete(str_replace('/storage/', 'public/', $oldImage));
//            }
//
//            $candidate->save();
//
//            return $candidate;
//        } catch (Exception $e) {
//            Log::error('Error updating candidate: ' . $e->getMessage());
//            throw $e;
//        }
//    }
//
//    public function deleteCandidate(Candidate $candidate): void
//    {
//        try {
//            $candidate->delete();
//        } catch (Exception $e) {
//            Log::error('Error deleting candidate: ' . $e->getMessage());
//            throw $e;
//        }
//    }
//}
