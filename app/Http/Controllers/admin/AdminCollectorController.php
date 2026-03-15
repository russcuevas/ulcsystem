<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCollectorController extends Controller
{
    public function AdminCollectorPage()
    {
        $collectors = DB::table('collectors')->get();

        $collectorAreas = DB::table('areas')
            ->join('collectors', 'collectors.id', '=', 'areas.collector_id')
            ->select(
                'areas.collector_id',
                'areas.location_name',
                'areas.areas_name',
                'collectors.fullname as collector_name'
            )
            ->get();

        return view('admin.collector.index', compact('collectors', 'collectorAreas'));
    }

    // Update collector name
    public function AdminUpdateCollector(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
        ]);

        DB::table('collectors')->where('id', $id)->update([
            'fullname' => $request->fullname
        ]);

        return redirect()->back()->with('success', 'Collector updated successfully!');
    }
}
