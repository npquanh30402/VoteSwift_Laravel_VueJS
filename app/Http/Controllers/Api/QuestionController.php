<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\VotingRoom;
use App\Services\QuestionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    protected QuestionService $questionService;

    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }

    public function index(VotingRoom $room): ?JsonResponse
    {
        try {
            $questions = $this->questionService->getRoomQuestions($room);

            return response()->json([
                'data' => $questions,
                'message' => 'Questions retrieved successfully',
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error getting questions: ' . $e->getMessage()], 500);
        }
    }

    public function update(Question $question, Request $request): ?JsonResponse
    {
        try {
            $question = $this->questionService->updateQuestion($question, $request);

            return response()->json([
                'data' => $question->decryptQuestion(),
                'message' => 'Question updated successfully',
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error updating question: ' . $e->getMessage()], 500);
        }
    }

    public function delete(Question $question): ?JsonResponse
    {
        try {
            $this->questionService->deleteQuestion($question);

            return response()->json(['message' => 'Question deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error deleting question: ' . $e->getMessage()], 500);
        }
    }

    public function store(VotingRoom $room, Request $request): ?JsonResponse
    {
        try {
            $question = $this->questionService->storeQuestion($room, $request);

            if ($question) {
                return response()->json([
                    'data' => $question->decryptQuestion(),
                    'message' => 'Question created successfully',
                ], 201);
            }

            return response()->json(['error' => 'Error creating question'], 500);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error creating question: ' . $e->getMessage()], 500);
        }
    }
}
