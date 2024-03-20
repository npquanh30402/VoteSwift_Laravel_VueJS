<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\VotingRoom;
use App\Services\QuestionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class QuestionController extends Controller
{
    protected $questionService;

    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }

    public function main(VotingRoom $room)
    {
        $questions = $room->questions->map(function ($question) {
            $question->question_title = Crypt::decryptString($question->question_title);
            $question->question_description = Crypt::decryptString($question->question_description);
            return $question;
        });

        $room->room_name = Crypt::decryptString(strip_tags($room->room_name));
        
        return view('voting.question.main', compact('room', 'questions'));
    }

    public function update(Question $question, Request $request)
    {
        try {
            $this->questionService->updateQuestion($question, $request);
            return back()->with('success', 'Question updated successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Error updating the question');
        }
    }

    public function delete(Question $question)
    {
        try {
            $this->questionService->deleteQuestion($question);
            return back()->with('success', 'Question deleted successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Error deleting the question');
        }
    }

    public function store(VotingRoom $room, Request $request)
    {
        try {
            $this->questionService->createQuestion($room, $request);
            return back()->with('success', 'Question added successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Error adding the question');
        }
    }
}
