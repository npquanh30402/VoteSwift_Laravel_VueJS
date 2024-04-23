<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Music;
use App\Services\HelperService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MusicController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = Auth::user();

            if (!$authUser) {
                throw new RuntimeException('User not found.');
            }

            $request->validate([
                'music' => 'required|file|mimes:mp3,flac,wav',
            ]);

            $fileName = $authUser->id . '-' . uniqid('', true) . '.' . $request->music->getClientOriginalExtension();
            $fileName = HelperService::sanitizeFileName($fileName);
            $oriFileName = $request->music->getClientOriginalName();
            $request->music->storeAs('uploads/music', $fileName, 'public');
            $authUser->music()->create([
                'user_id' => $authUser->id,
                'title' => $oriFileName,
                'url' => $fileName,
            ]);

            DB::commit();

            return response()->json([
                'data' => $fileName,
                'message' => 'Music uploaded successfully.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Music $music)
    {
        DB::beginTransaction();
        try {
            $musicUrl = str_replace('/storage/', 'public/', $music->url);
            
            $music->delete();

            DB::commit();

            Storage::delete($musicUrl);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
