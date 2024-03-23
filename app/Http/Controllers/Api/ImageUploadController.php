<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ImageUploadController extends Controller
{
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
        if ($request->hasFile('image')) {
            $fileName = uniqid('', true) . '.' . $request->image->getClientOriginalExtension();
            $request->image->storeAs('uploads/images', $fileName, 'public');

            $imageUrl = '/storage/uploads/images/' . $fileName;

            return response()->json([
                'message' => 'Image uploaded successfully',
                'image' => $imageUrl
            ], 200);
        }

        return response()->json(['message' => 'No image file found'], 400);
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
