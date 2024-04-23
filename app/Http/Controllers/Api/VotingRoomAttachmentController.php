<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VotingRoom;
use App\Models\VotingRoomFiles;
use App\Services\HelperService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VotingRoomAttachmentController extends Controller
{
    public function index(VotingRoom $room)
    {
        try {
            $attachments = $room->attachments;

            return response()->json([
                'data' => $attachments,
                'message' => 'Attachments retrieved successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(VotingRoom $room, Request $request)
    {
        $this->authorize('create', $room);

        DB::beginTransaction();
        try {
            if ($request->hasFile('file')) {
                $file = $request->file;
                $fileName = $room->id . '-' . uniqid('', true) . '.' . $file->getClientOriginalExtension();

                $fileName = HelperService::sanitizeFileName($fileName);

                $file->storeAs('uploads/rooms', $fileName, 'public');

                $oriFileName = $file->getClientOriginalName();

                $attachment = $room->attachments()->create([
                    'voting_room_id' => $room->id,
                    'file_name' => $oriFileName,
                    'file_path' => $fileName,
                ]);

                DB::commit();

                return response()->json([
                    'data' => $attachment,
                    'message' => 'Attachment uploaded successfully',
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function delete(VotingRoomFiles $attachment)
    {
        DB::beginTransaction();
        try {
            $attachment->delete();

            $oldFile = $attachment->file_path;

            if ($oldFile) {
                Storage::delete(str_replace('/storage/', 'public/', $oldFile));
            }

            DB::commit();

            return response()->json([
                'message' => 'Attachment deleted successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
