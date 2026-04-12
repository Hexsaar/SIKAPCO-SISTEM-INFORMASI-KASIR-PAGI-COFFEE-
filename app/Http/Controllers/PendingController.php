<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PendingController extends Controller
{
    public function index()
    {
        return view('pending.index');
    }
}
