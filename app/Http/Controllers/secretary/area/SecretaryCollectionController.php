<?php

namespace App\Http\Controllers\secretary\area;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class SecretaryCollectionController extends Controller
{
    public function CollectionReferencesPage($areaId)
    {
        // Get currently logged-in secretary
        $secretary = Session::get('user');
        if (!$secretary) {
            return redirect('/login')->with('error', 'Please login first');
        }

        $secretary_id = $secretary->id;

        // Get area info **only if assigned to this secretary**
        $area = DB::table('areas')
            ->where('id', $areaId)
            ->where('secretary_id', $secretary_id)
            ->first();

        if (!$area) {
            return redirect()->route('secretary.areas.page')
                ->with('error', 'You are not authorized to access this area.');
        }

        // Get references with collector name and total clients per reference
        $references = DB::table('clients_payments as cp')
            ->leftJoin('collectors as col', 'cp.collected_by', '=', 'col.id')
            ->where('cp.client_area', $areaId)
            ->select(
                'cp.reference_number',
                'cp.due_date',
                'cp.collected_by',
                'col.fullname as collected_by_name',
                DB::raw('COUNT(cp.client_id) as total_clients') // total clients per reference
            )
            ->groupBy('cp.reference_number', 'cp.due_date', 'cp.collected_by', 'col.fullname')
            ->orderBy('cp.due_date', 'desc')
            ->get();

        return view('secretary.areas.collections_references', [
            'references' => $references,
            'areaId' => $areaId,
            'location_name' => $area->location_name ?? 'N/A',
            'areas_name' => $area->areas_name ?? 'N/A'
        ]);
    }

    // Step 2: Show collection details for a reference
    public function CollectionDetailPage($referenceNumber)
    {
        $payments = DB::table('clients_payments as p')
            ->join('clients as c', 'p.client_id', '=', 'c.id')
            ->join('clients_loans as l', 'p.client_loans_id', '=', 'l.id')
            ->where('p.reference_number', $referenceNumber)
            ->select(
                'p.*',
                'c.fullname',
                'l.balance as loan_balance',
                'l.daily as loan_daily'
            )
            ->get();

        return view('secretary.areas.collection_detail', compact('payments', 'referenceNumber'));
    }
}
