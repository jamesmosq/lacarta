<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('central.home');
    }
}
