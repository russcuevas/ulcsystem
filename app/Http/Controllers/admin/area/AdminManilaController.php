<?php

namespace App\Http\Controllers\admin\area;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminManilaController extends Controller
{
    public function AdminManilaPage()
    {
        $manilaAreas = DB::table('areas')
            ->join('collectors', 'collectors.id', '=', 'areas.collector_id')
            ->where('areas.location_name', 'Manila Area')
            ->select('areas.id', 'areas.areas_name', 'collectors.fullname as collector_name')
            ->get();

        return view('admin.areas.manila.index', compact('manilaAreas'));
    }
}
