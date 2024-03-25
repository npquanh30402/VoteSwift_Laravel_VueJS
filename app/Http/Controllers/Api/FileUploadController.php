<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VotingRoom;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function storeAttachment(VotingRoom $room, Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file;
            $fileName = $room->id . '-' . uniqid('', true) . '.' . $file->getClientOriginalExtension();

            $fileName = HelperService::sanitizeFileName($fileName);

            $file->storeAs('uploads/rooms', $fileName, 'public');

            $oriFileName = $file->getClientOriginalName();

            $room->attachments()->create([
                'voting_room_id' => $room->id,
                'file_name' => $oriFileName,
                'file_path' => $fileName,
            ]);

            return response()->json([
                'message' => 'Image uploaded successfully',
            ], 200);
        }

        return response()->json(['message' => 'No image file found'], 400);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
