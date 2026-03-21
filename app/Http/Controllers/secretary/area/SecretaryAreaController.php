<?php

namespace App\Http\Controllers\secretary\area;

use App\Http\Controllers\Controller;
use App\Models\Areas;
use Illuminate\Support\Facades\Session;


class SecretaryAreaController extends Controller
{
    public function index()
    {
        $secretary = Session::get('user');

        $secretaryId = $secretary->id;

        $areas = Areas::where('secretary_id', $secretaryId)->get();

        return view('secretary.areas.index', compact('areas'));
    }
}
