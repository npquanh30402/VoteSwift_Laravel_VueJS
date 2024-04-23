<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ImageUploadController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$request->hasFile('image')) {
                throw new RuntimeException('No image uploaded');
            }

            $fileName = uniqid('', true) . '.' . $request->image->getClientOriginalExtension();

            $fileName = HelperService::sanitizeFileName($fileName);

            $request->image->storeAs('uploads/images', $fileName, 'public');

            $imageUrl = '/storage/uploads/images/' . $fileName;

            DB::commit();

            return response()->json([
                'image' => $imageUrl,
                'message' => 'Image uploaded successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
