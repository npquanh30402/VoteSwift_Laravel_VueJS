<?php

namespace App\Services;

use App\Models\Question;
use App\Models\VotingRoom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QuestionService
{
    public function createQuestion(VotingRoom $room, Request $request): void
    {
        try {
            $question = new Question();

            $question->question_title = HelperService::encryptAndStripTags($request->question_title);

            $question->question_description = HelperService::encryptAndStripTags($request->question_description);

            if ($request->hasFile('question_image')) {
                $fileName = uniqid('', true) . '.' . $request->question_image->getClientOriginalExtension();

                $fileName = HelperService::sanitizeFileName($fileName);

                $request->question_image->storeAs('uploads/questions', $fileName, 'public');
                $question->question_image = $fileName;
            }

            $question->allow_multiple_votes = $request->allow_multiple_votes;

            $question->voting_room_id = $room->id;

            $question->save();
        } catch (Exception $e) {
            Log::debug('Error creating question: ' . $e->getMessage());
        }
    }

    public function updateQuestion(Question $question, Request $request)
    {
        try {
            $question->question_title = HelperService::encryptAndStripTags($request->question_title);

            $question->question_description = HelperService::encryptAndStripTags($request->question_description);

            $oldImage = $question->question_image;
            if ($request->hasFile('question_image')) {

                $fileName = uniqid('', true) . '.' . $request->question_image->getClientOriginalExtension();

                $fileName = HelperService::sanitizeFileName($fileName);

                $request->question_image->storeAs('uploads/questions', $fileName, 'public');
                $question->question_image = $fileName;
            }

            if ($oldImage !== $question->question_image) {
                Storage::delete(str_replace('/storage/', 'public/', $oldImage));
            }

            $question->allow_multiple_votes = $request->allow_multiple_votes;

            $question->save();
        } catch (Exception $e) {
            Log::debug('Error updating question: ' . $e->getMessage());
        }
    }

    public function deleteQuestion(Question $question): void
    {
        try {
            $question->delete();
        } catch (Exception $e) {
            Log::debug('Error deleting question: ' . $e->getMessage());
        }
    }
}
