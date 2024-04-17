<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class UserMessageController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/UserChat');
    }
}
