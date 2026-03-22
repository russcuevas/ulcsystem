<?php

namespace App\Http\Controllers\collector;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class CollectorCollectionController extends Controller
{
    /**
     * Show the collections page.
     */
    public function CollectorCollectionPage(Request $request)
    {
        $user = Session::get('user');
        $collectorId = $user->id;

        $selectedDate = $request->date ?? now()->format('Y-m-d'); // default today
        $today = \Carbon\Carbon::parse($selectedDate);

        // Get all areas assigned to collector
        $areas = DB::table('areas')
            ->where('collector_id', $collectorId)
            ->get(['id', 'location_name', 'areas_name']);

        $area = $areas->first();
        $areaIds = $areas->pluck('id')->toArray();

        // Get latest loan per client where loan_from <= selected date <= loan_to AND balance > 0
        $loans = DB::table('clients_loans as cl')
            ->join('clients as c', 'cl.client_id', '=', 'c.id')
            ->whereIn('c.area_id', $areaIds)
            ->where('cl.balance', '>', 0)
            ->whereDate('cl.loan_from', '<=', $selectedDate)
            ->whereDate('cl.loan_to', '>=', $selectedDate)
            ->select('cl.*', 'c.fullname', 'c.area_id')
            ->orderByDesc('cl.id')
            ->get();

        // Get payments already made for this selected date
        $payments = DB::table('clients_payments')
            ->whereDate('due_date', $selectedDate)
            ->whereIn('client_id', $loans->pluck('client_id')->toArray())
            ->get()
            ->keyBy('client_id'); // easy lookup per client

        // Map loans with payments
        $clients = $loans->map(function ($loan) use ($areas, $payments) {
            $area = $areas->firstWhere('id', $loan->area_id);
            $payment = $payments[$loan->client_id] ?? null;

            return (object) [
                'id' => $loan->client_id,
                'fullname' => $loan->fullname,
                'area_id' => $loan->area_id,
                'location_name' => $area->location_name ?? 'N/A',
                'loan' => $loan,
                'payment' => $payment,
            ];
        });

        $areas_name = $area->areas_name ?? 'UNKNOWN';
        $dateCode = \Carbon\Carbon::parse($selectedDate)->format('Y-m-d');
        $refNo = $dateCode . '-' . strtoupper($areas_name) . '-001';

        // Total clients for the table
        $totalClients = $clients->count();

        // Total collections for selected date (already collected)
        $totalCollections = DB::table('clients_payments')
            ->whereDate('due_date', $selectedDate)
            ->whereIn('client_id', $clients->pluck('id')->toArray())
            ->sum('collection');

        // Total collectibles from daily of loans
        $totalDailyCollectibles = $clients->sum(function ($client) {
            return $client->loan->daily ?? 0;
        });

        return view('collector.collection.index', compact(
            'clients',
            'area',
            'refNo',
            'selectedDate',
            'totalClients',
            'totalCollections',
            'totalDailyCollectibles'
        ));
    }

    /**
     * Store a single client's collection.
     */
    public function store(Request $request)
    {
        $user = Session::get('user');

        $request->validate([
            'collection' => 'required|numeric|min:0',
            'type' => 'required|string'
        ]);

        DB::table('clients_payments')->insert([
            'reference_number' => $request->reference_no,
            'collected_by' => $user->id,
            'due_date' => $request->due_date,
            'client_id' => $request->client_id,
            'client_loans_id' => $request->loan_id,
            'client_area' => $request->area_id,
            'daily' => $request->daily,
            'old_balance' => $request->old_balance,
            'collection' => $request->collection,
            'type' => $request->type,
            'is_collected' => 0,
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Saved successfully!');
    }
}
