<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HomeController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        return Inertia::render('Index/Index');
    }
}
