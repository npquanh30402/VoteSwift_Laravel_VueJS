<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CandidateRequest;
use App\Models\Candidate;
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

class CandidateController extends Controller
{
//    protected CandidateService $candidateService;
//
//    public function __construct(CandidateService $candidateService)
//    {
//        $this->candidateService = $candidateService;
//    }

//    public function QuestionCandidates(Question $question): ?JsonResponse
//    {
//        try {
//            $candidates = $question->candidates()->get();
//
//            $candidates->each(function ($candidate) {
//                $candidate->decryptCandidate();
//            });
//
//            return response()->json([
//                'data' => $candidates,
//                'message' => 'Question candidates retrieved successfully',
//            ]);
//        } catch (Exception $e) {
//            return response()->json([
//                'message' => $e->getMessage(),
//            ], 500);
//        }
//    }

    public function importCandidatesFromCSV(Question $question, Request $request)
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

            $candidates = [];

            foreach ($data as $iValue) {
                $candidate = new Candidate();

                $candidate->candidate_title = HelperService::encryptAndStripTags($iValue[0]);
                $candidate->candidate_description = HelperService::encryptAndStripTags($iValue[1]);
                $candidate->question_id = $question->id;

                $candidate->save();

                $candidates[] = $candidate->decryptCandidate();
            }

            $reader->close();

            unlink($file->getPathname());

            DB::commit();

            return response()->json([
                'data' => $candidates,
                'message' => 'Questions imported successfully.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function RoomCandidates(VotingRoom $room): JsonResponse
    {
        try {
            $questions = $room->questions;

            $candidates = [];

            foreach ($questions as $question) {
                $questionCandidates = $question->candidates()->get();

                $questionCandidates->each(function ($candidate) {
                    $candidate->decryptCandidate();
                });

                $candidates[$question->id] = $questionCandidates;
            }

            return response()->json([
                'data' => $candidates,
                'message' => 'Room candidates retrieved successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Question $question, CandidateRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $candidate = new Candidate();

            $candidate->candidate_title = HelperService::encryptAndStripTags($request->candidate_title);

            if ($request->candidate_description) {
                $candidate->candidate_description = HelperService::encryptAndStripTags($request->candidate_description);
            }

            $candidate->question_id = $question->id;

            if ($request->hasFile('candidate_image')) {
                $fileName = uniqid('', true) . '.' . $request->candidate_image->getClientOriginalExtension();

                $fileName = HelperService::sanitizeFileName($fileName);

                $request->candidate_image->storeAs('uploads/candidates', $fileName, 'public');
                $candidate->candidate_image = $fileName;
            }

            $candidate->save();

            DB::commit();

            return response()->json([
                'data' => $candidate->decryptCandidate(),
                'message' => 'Candidate created successfully',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Candidate $candidate, CandidateRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $candidate->candidate_title = HelperService::encryptAndStripTags($request->candidate_title);

            $candidate->candidate_description = HelperService::encryptAndStripTags($request->candidate_description);

            if ($request->hasFile('candidate_image')) {
                $oldImage = $candidate->candidate_image;
                $fileName = uniqid('', true) . '.' . $request->candidate_image->getClientOriginalExtension();

                $fileName = HelperService::sanitizeFileName($fileName);

                $request->candidate_image->storeAs('uploads/candidates', $fileName, 'public');
                $candidate->candidate_image = $fileName;

                if ($oldImage !== $candidate->candidate_image) {
                    Storage::delete(str_replace('/storage/', 'public/', $oldImage));
                }
            } else if (HelperService::convertNullStringToNull($request->candidate_image) === null) {
                Storage::delete(str_replace('/storage/', 'public/', $request->candidate_image));
                $candidate->candidate_image = null;
            }

            $candidate->save();

            DB::commit();

            return response()->json([
                'data' => $candidate->decryptCandidate(),
                'message' => 'Candidate updated successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Candidate $candidate): JsonResponse
    {
        DB::beginTransaction();
        try {
            $candidate->delete();

            DB::commit();

            return response()->json(['message' => 'Candidate deleted successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
