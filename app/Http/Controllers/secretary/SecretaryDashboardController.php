<?php

namespace App\Http\Controllers\secretary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SecretaryDashboardController extends Controller
{
    public function SecretaryDashboardPage()
    {
        return view('secretary.dashboard.index');
    }
}
