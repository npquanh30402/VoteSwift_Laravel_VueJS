<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\VotingRoom;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index(VotingRoom $room)
    {
        $roomQuestions = $room->questions->each(function ($question) {
            $question->decryptQuestion();
        });

        return response()->json($roomQuestions);
    }

    public function update(Question $question, Request $request)
    {
        $question->question_title = HelperService::encryptAndStripTags($request->question_title);

        $question->question_description = HelperService::encryptAndStripTags($request->question_description);

        if ($request->allow_multiple_votes) {
            $question->allow_multiple_votes = $request->allow_multiple_votes;
        }

        if ($request->allow_skipping) {
            $question->allow_skipping = $request->allow_skipping;
        }

        if ($request->hasFile('question_image')) {
            $oldImage = $question->question_image;
            $fileName = uniqid('', true) . '.' . $request->question_image->getClientOriginalExtension();

            $fileName = HelperService::sanitizeFileName($fileName);

            $request->question_image->storeAs('uploads/questions', $fileName, 'public');
            $question->question_image = $fileName;

            if ($oldImage !== $question->question_image) {
                Storage::delete(str_replace('/storage/', 'public/', $oldImage));
            }
        }

        $question->save();

        return response()->json($question->decryptQuestion());
    }

    public function delete(Question $question)
    {
        $question->delete();

        return response()->json(null, 204);
    }

    public function store(VotingRoom $room, Request $request)
    {
        $question = new Question();

        $question->question_title = HelperService::encryptAndStripTags($request->question_title);

        if (isset($request->question_description)) {
            $question->question_description = HelperService::encryptAndStripTags($request->question_description);
        }

        if ($request->allow_multiple_votes) {
            $question->allow_multiple_votes = $request->allow_multiple_votes;
        }

        if ($request->allow_skipping) {
            $question->allow_skipping = $request->allow_skipping;
        }

        if ($request->hasFile('question_image')) {
            $fileName = uniqid('', true) . '.' . $request->question_image->getClientOriginalExtension();

            $fileName = HelperService::sanitizeFileName($fileName);

            $request->question_image->storeAs('uploads/questions', $fileName, 'public');
            $question->question_image = $fileName;
        }

        $question->voting_room_id = $room->id;

        $question->save();

        return response()->json($question->decryptQuestion(), 201);
    }
}
