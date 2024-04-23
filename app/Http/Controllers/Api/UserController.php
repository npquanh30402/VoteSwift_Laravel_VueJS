<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function searchUser(Request $request)
    {
        try {
            $query = $request->input('query');

            $users = User::where('id', $query)
                ->orWhere('username', 'like', "%$query%")
                ->orWhere('email', 'like', "%$query%")
                ->get();

            return response()->json([
                'data' => $users,
                'message' => 'success'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
