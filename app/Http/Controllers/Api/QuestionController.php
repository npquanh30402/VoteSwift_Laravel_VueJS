<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Models\Question;
use App\Models\VotingRoom;
use App\Services\HelperService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\CSV\Reader;
use RuntimeException;

class QuestionController extends Controller
{
//    protected QuestionService $questionService;
//
//    public function __construct(QuestionService $questionService)
//    {
//        $this->questionService = $questionService;
//    }

    public function importQuestionsFromCSV(VotingRoom $room, Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$request->hasFile('csv_file')) {
                throw new RuntimeException('File not found');
            }

            $file = $request->file('csv_file');

            $reader = new Reader();
            $reader->open($file->getPathname());

            $data = [];

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $rowData = [];
                    foreach ($row->getCells() as $cell) {
                        $rowData[] = $cell->getValue();
                    }
                    $data[] = $rowData;
                }
            }

            $questions = [];

            foreach ($data as $iValue) {
                $question = new Question();

                $question->question_title = HelperService::encryptAndStripTags($iValue[0]);
                $question->question_description = HelperService::encryptAndStripTags($iValue[1]);
                $question->allow_multiple_votes = $iValue[2];
                $question->allow_skipping = $iValue[3];
                $question->voting_room_id = $room->id;

                $question->save();

                $questions[] = $question->decryptQuestion();
            }

            $reader->close();

            unlink($file->getPathname());

            DB::commit();

            return response()->json([
                'data' => $questions,
                'message' => 'Questions imported successfully.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function index(VotingRoom $room): ?JsonResponse
    {
        try {
            $questions = $room->questions->each(function ($question) {
                $question->decryptQuestion();
            });

            return response()->json([
                'data' => $questions,
                'message' => 'Questions retrieved successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Question $question, QuestionRequest $request): ?JsonResponse
    {
        DB::beginTransaction();
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
            } else if (HelperService::convertNullStringToNull($request->question_image) === null) {
                Storage::delete(str_replace('/storage/', 'public/', $question->question_image));
                $question->question_image = null;
            }

            $question->save();

            DB::commit();

            return response()->json([
                'data' => $question->decryptQuestion(),
                'message' => 'Question updated successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Question $question): ?JsonResponse
    {
        DB::beginTransaction();
        try {
            $question->delete();

            DB::commit();

            return response()->json(['message' => 'Question deleted successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(VotingRoom $room, QuestionRequest $request): ?JsonResponse
    {
        DB::beginTransaction();
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

            DB::commit();

            return response()->json([
                'data' => $question->decryptQuestion(),
                'message' => 'Question created successfully',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
