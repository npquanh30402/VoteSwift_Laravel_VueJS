<?php

namespace App\Http\Controllers;

use App\Models\Music;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MusicController extends Controller
{
    public function uploadMusic(Request $request)
    {
        $authUser = Auth::user();

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
    }

    public function deleteMusic(Music $music)
    {
        Storage::delete(str_replace('/storage/', 'public/', $music->url));
        $music->delete();
    }
}
