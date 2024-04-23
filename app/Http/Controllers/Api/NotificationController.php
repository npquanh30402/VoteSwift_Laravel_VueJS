<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $notifications = $request->user()->notifications()->paginate(10);

            return response()->json([
                'data' => $notifications,
                'message' => 'Notifications retrieved successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function unreadCount(User $user)
    {
        try {
            $count = $user->unreadNotifications()->count();

            return response()->json([
                'data' => $count,
                'message' => 'Unread notifications count retrieved successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAllAsRead(User $user)
    {
        DB::beginTransaction();
        try {
            $user->unreadNotifications->markAsRead();

            DB::commit();

            return response()->json([
                'message' => 'All notifications marked as read',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
