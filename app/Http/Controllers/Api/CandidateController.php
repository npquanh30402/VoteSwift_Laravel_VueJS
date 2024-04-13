<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CandidateRequest;
use App\Models\Candidate;
use App\Models\Question;
use App\Models\VotingRoom;
use App\Services\CandidateService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    protected CandidateService $candidateService;

    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
    }

    public function QuestionCandidates(Question $question): ?JsonResponse
    {
        try {
            $candidates = $this->candidateService->getQuestionCandidates($question);

            return response()->json($candidates);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error getting candidates: ' . $e->getMessage()], 500);
        }
    }

    public function RoomCandidates(VotingRoom $room): JsonResponse
    {
        try {
            $candidates = $this->candidateService->getRoomCandidates($room);

            return response()->json($candidates);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error getting candidates: ' . $e->getMessage()], 500);
        }
    }

    public function store(Question $question, CandidateRequest $request): JsonResponse
    {
        try {
            $candidate = $this->candidateService->storeCandidate($question, $request);

            $candidate->decryptCandidate();

            return response()->json($candidate, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error creating candidate: ' . $e->getMessage()], 500);
        }
    }

    public function update(Candidate $candidate, Request $request): JsonResponse
    {
        try {
            $candidate = $this->candidateService->updateCandidate($candidate, $request);

            $candidate->decryptCandidate();

            return response()->json($candidate);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error updating candidate: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Candidate $candidate): JsonResponse
    {
        try {
            $this->candidateService->deleteCandidate($candidate);

            return response()->json(['message' => 'Candidate deleted successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error deleting candidate: ' . $e->getMessage()], 500);
        }
    }
}
