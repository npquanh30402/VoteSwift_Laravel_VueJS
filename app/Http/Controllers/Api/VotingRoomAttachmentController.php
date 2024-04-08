<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VotingRoom;
use App\Models\VotingRoomFiles;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VotingRoomAttachmentController extends Controller
{
    public function index(VotingRoom $room)
    {
        $attachments = $room->attachments;

        return response()->json($attachments);
    }

    public function store(VotingRoom $room, Request $request)
    {
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

            return response()->json($attachment);
        }

        return response()->json(null, 400);
    }

    public function destroy(VotingRoomFiles $attachment)
    {
        $attachment->delete();

        $oldFile = $attachment->file_path;

        if ($oldFile) {
            Storage::delete(str_replace('/storage/', 'public/', $oldFile));
        }

        return response()->json(null, 204);
    }
}
