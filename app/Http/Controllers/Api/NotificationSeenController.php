<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;

class NotificationSeenController extends Controller
{
    public function __invoke(DatabaseNotification $notification)
    {
        DB::beginTransaction();
        try {
            $notification->markAsRead();

            DB::commit();

            return response()->json([
                'message' => 'Notification marked as read',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
