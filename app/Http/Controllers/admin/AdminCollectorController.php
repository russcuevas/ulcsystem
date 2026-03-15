<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminCollectorController extends Controller
{
    public function AdminCollectorPage()
    {
        return view('admin.collector.index');
    }
}
