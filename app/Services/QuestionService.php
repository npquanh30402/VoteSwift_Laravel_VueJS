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
    /**
     * @throws Exception
     */
    public function getRoomQuestions(VotingRoom $room)
    {
        try {
            return $room->questions->each(function ($question) {
                $question->decryptQuestion();
            });
        } catch (Exception $e) {
            Log::error('Error getting room questions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function updateQuestion(Question $question, Request $request): ?Question
    {
        try {
            $question->question_title = HelperService::encryptAndStripTags($request->question_title);

            $question->question_description = HelperService::encryptAndStripTags($request->question_description);

            if ($request->allow_multiple_votes) {
                $question->allow_multiple_votes = $request->allow_multiple_votes ? 1 : 0;
            }

            if ($request->allow_skipping) {
                $question->allow_skipping = $request->allow_skipping ? 1 : 0;
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

            return $question;
        } catch (Exception $e) {
            Log::error('Error updating question: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function storeQuestion(VotingRoom $room, Request $request): ?Question
    {
        try {
            $question = new Question();

            $question->question_title = HelperService::encryptAndStripTags($request->question_title);

            if (isset($request->question_description)) {
                $question->question_description = HelperService::encryptAndStripTags($request->question_description);
            }

            if ($request->allow_multiple_votes) {
                $question->allow_multiple_votes = $request->allow_multiple_votes ? 1 : 0;
            }

            if ($request->allow_skipping) {
                $question->allow_skipping = $request->allow_skipping ? 1 : 0;
            }

            if ($request->hasFile('question_image')) {
                $fileName = uniqid('', true) . '.' . $request->question_image->getClientOriginalExtension();

                $fileName = HelperService::sanitizeFileName($fileName);

                $request->question_image->storeAs('uploads/questions', $fileName, 'public');
                $question->question_image = $fileName;
            }

            $question->voting_room_id = $room->id;

            $question->save();

            return $question;
        } catch (Exception $e) {
            Log::error('Error creating question: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteQuestion(Question $question): void
    {
        try {
            $question->delete();
        } catch (Exception $e) {
            Log::error('Error deleting question: ' . $e->getMessage());
            throw $e;
        }
    }
}
