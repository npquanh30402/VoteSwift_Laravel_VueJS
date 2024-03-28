<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(10);
//        dd($notifications->toArray());

        return Inertia::render('Users/Notification/Index', compact('notifications'));
    }
}
