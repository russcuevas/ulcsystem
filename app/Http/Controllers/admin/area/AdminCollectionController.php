<?php

namespace App\Http\Controllers\admin\area;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AdminCollectionController extends Controller
{
    public function AdminCollectionReferencesPage($areaId)
    {
        $area = DB::table('areas')
            ->where('id', $areaId)
            ->first();

        if (!$area) {
            return redirect()->back()->with('error', 'Area not found.');
        }

        $references = DB::table('clients_payments as cp')
            ->leftJoin('collectors as col', 'cp.collected_by', '=', 'col.id')
            ->where('cp.client_area', $areaId)
            ->select(
                'cp.reference_number',
                'cp.due_date',
                'cp.collected_by',
                'col.fullname as collected_by_name'
            )
            ->groupBy('cp.reference_number', 'cp.due_date', 'cp.collected_by', 'col.fullname')
            ->orderBy('cp.due_date', 'desc')
            ->get();

        $references = $references->map(function ($ref) use ($areaId) {
            $loans = DB::table('clients_loans as cl')
                ->join('clients as c', 'cl.client_id', '=', 'c.id')
                ->where('c.area_id', $areaId)
                ->whereDate('cl.loan_from', '<=', $ref->due_date)
                ->select('cl.*', 'c.id as client_id')
                ->get();

            $payments = DB::table('clients_payments')
                ->where('client_area', $areaId)
                ->where('reference_number', $ref->reference_number)
                ->get()
                ->keyBy('client_id');

            $filteredClients = $loans->filter(function ($loan) use ($payments) {
                $balance = $loan->balance ?? 0;
                $payment = $payments[$loan->client_id] ?? null;

                return $balance > 0 || ($balance <= 0 && $payment && ($payment->collection ?? 0) > 0);
            });

            $ref->total_clients = $filteredClients->count();

            $ref->total_collections = $filteredClients->sum(function ($loan) use ($payments) {
                $payment = $payments[$loan->client_id] ?? null;
                return $payment ? ($payment->collection ?? 0) : 0;
            });

            $ref->total_daily_collectibles = $filteredClients->sum(function ($loan) {
                return $loan->daily ?? 0;
            });

            return $ref;
        });

        return view('admin.areas.collections_references', [
            'references' => $references,
            'areaId' => $areaId,
            'location_name' => $area->location_name ?? 'N/A',
            'areas_name' => $area->areas_name ?? 'N/A'
        ]);
    }

    public function AdminCollectionDetailPage($referenceNumber)
    {
        $reference = DB::table('clients_payments')
            ->where('reference_number', $referenceNumber)
            ->first();

        if (!$reference) {
            return redirect()->back()->with('error', 'Reference not found.');
        }

        $area = DB::table('areas')
            ->where('id', $reference->client_area)
            ->first();

        if (!$area) {
            return redirect()->back()->with('error', 'Area not found.');
        }

        $selectedDate = $reference->due_date;

        $loans = DB::table('clients_loans as cl')
            ->join('clients as c', 'cl.client_id', '=', 'c.id')
            ->where('c.area_id', $area->id)
            ->whereDate('cl.loan_from', '<=', $selectedDate)
            ->select(
                'cl.*',
                'c.fullname',
                'c.id as client_id'
            )
            ->orderByDesc('cl.id')
            ->get();

        $payments = DB::table('clients_payments')
            ->where('reference_number', $referenceNumber)
            ->get()
            ->keyBy('client_id');

        $clients = $loans->map(function ($loan) use ($payments, $selectedDate) {
            $payment = $payments[$loan->client_id] ?? null;

            $isOverdue = \Carbon\Carbon::parse($selectedDate)
                ->gt(\Carbon\Carbon::parse($loan->loan_to));

            return (object)[
                'id' => $loan->client_id,
                'fullname' => $loan->fullname,
                'loan' => $loan,
                'payment' => $payment,
                'is_overdue' => $isOverdue
            ];
        });

        $clients = $clients->filter(function ($c) {
            $balance = $c->loan->balance ?? 0;
            return $balance > 0 || ($balance <= 0 && $c->payment && ($c->payment->collection ?? 0) > 0);
        })->values();

        $totalClients = $clients->count();
        $totalCollections = $clients->sum(function ($c) {
            return $c->payment->collection ?? 0;
        });
        $totalDailyCollectibles = $clients->sum(function ($c) {
            return $c->loan->daily ?? 0;
        });

        return view('admin.areas.collection_detail', [
            'clients' => $clients,
            'referenceNumber' => $referenceNumber,
            'location_name' => $area->location_name ?? 'N/A',
            'areas_name' => $area->areas_name ?? 'N/A',
            'totalClients' => $totalClients,
            'totalCollections' => $totalCollections,
            'totalDailyCollectibles' => $totalDailyCollectibles,
            'selectedDate' => $selectedDate,
            'refNo' => $referenceNumber,
            'areaId' => $area->id
        ]);
    }

    public function AdminCollectClientsPayment(Request $request, $refNo)
    {
        $action = $request->input('action');

        $reference = DB::table('clients_payments')->where('reference_number', $refNo)->first();
        if (!$reference) {
            return response()->json(['message' => 'Reference not found'], 404);
        }

        $selectedDate = $reference->due_date;
        $areaId = $reference->client_area;

        $collector = DB::table('areas')
            ->where('id', $areaId)
            ->value('collector_id');

        if ($action === 'no_payment') {
            $loans = DB::table('clients_loans as cl')
                ->join('clients as c', 'cl.client_id', '=', 'c.id')
                ->where('c.area_id', $areaId)
                ->where('cl.balance', '>', 0)
                ->select('cl.*', 'c.id as client_id')
                ->get();
        } else {
            $loans = DB::table('clients_loans as cl')
                ->join('clients as c', 'cl.client_id', '=', 'c.id')
                ->where('c.area_id', $areaId)
                ->where('cl.balance', '>', 0)
                ->whereDate('cl.loan_from', '<=', $selectedDate)
                ->select('cl.*', 'c.id as client_id')
                ->get();
        }

        $payments = DB::table('clients_payments')
            ->where('reference_number', $refNo)
            ->whereIn('client_id', $loans->pluck('client_id'))
            ->get()
            ->keyBy('client_id');

        $user = Session::get('user');

        foreach ($loans as $loan) {
            $payment = $payments[$loan->client_id] ?? null;

            $isLapsed = \Carbon\Carbon::parse($selectedDate)
                ->gt(\Carbon\Carbon::parse($loan->loan_to)) ? 1 : 0;

            if ($action === 'no_payment') {
                if (\Carbon\Carbon::parse($loan->loan_from)->lte($selectedDate)) {
                    if (!$payment) {
                        DB::table('clients_payments')->insert([
                            'reference_number' => (string) $refNo,
                            'client_id' => $loan->client_id,
                            'client_loans_id' => $loan->id,
                            'client_area' => $areaId,
                            'collection' => 0,
                            'type' => 'NO PAYMENT',
                            'is_lapsed' => $isLapsed,
                            'is_collected' => 1,
                            'due_date' => $selectedDate,
                            'daily' => $loan->daily ?? 0,
                            'old_balance' => $loan->balance ?? 0,
                            'created_by' => $user->id ?? null,
                            'collected_by' => $collector,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    } else {
                        if (($payment->collection === null || $payment->collection == 0) && $payment->type !== 'NO PAYMENT') {
                            DB::table('clients_payments')
                                ->where('id', $payment->id)
                                ->update([
                                    'type' => 'NO PAYMENT',
                                    'is_lapsed' => $isLapsed,
                                    'is_collected' => 1,
                                    'updated_at' => now()
                                ]);
                        }
                    }
                }
            }

            if ($action === 'collect') {
                if ($payment && $payment->collection > 0 && $payment->is_collected == 0) {
                    $newBalance = $loan->balance - $payment->collection;
                    $newBalance = max($newBalance, 0);

                    DB::table('clients_payments')
                        ->where('id', $payment->id)
                        ->update([
                            'is_collected' => 1,
                            'is_lapsed' => $isLapsed,
                            'updated_at' => now()
                        ]);

                    DB::table('clients_loans')
                        ->where('id', $loan->id)
                        ->update([
                            'balance' => $newBalance,
                            'status' => $newBalance <= 0 ? 'paid' : 'unpaid',
                            'updated_at' => now()
                        ]);
                }
            }
        }

        $msg = $action === 'collect'
            ? 'Payment collected successfully for all applicable clients.'
            : 'All clients without payment are now tagged as NO PAYMENT.';

        return response()->json(['message' => $msg]);
    }

    public function AdminPrintCollection($refNo)
    {
        $reference = DB::table('clients_payments')
            ->where('reference_number', $refNo)
            ->first();

        if (!$reference) {
            abort(404, 'Reference not found.');
        }

        $area = DB::table('areas')
            ->where('id', $reference->client_area)
            ->first();

        if (!$area) {
            abort(404, 'Area not found.');
        }

        $payments = DB::table('clients_payments as cp')
            ->join('clients as c', 'cp.client_id', '=', 'c.id')
            ->join('clients_loans as cl', 'cp.client_loans_id', '=', 'cl.id')
            ->leftJoin('collectors as col', 'cp.collected_by', '=', 'col.id')
            ->where('cp.reference_number', $refNo)
            ->select(
                'cp.*',
                'c.fullname',
                'cl.loan_amount',
                'cl.balance',
                'cl.loan_to',
                'col.fullname as collected_by_name'
            )
            ->orderBy('c.fullname')
            ->get();

        if ($payments->isEmpty()) {
            abort(404, 'No payments found.');
        }

        return view('admin.areas.print.print_collection', [
            'payments' => $payments,
            'area' => $area,
            'referenceNumber' => $refNo
        ]);
    }

    public function AdminPrintSummaryCollection(Request $request, $areaId)
    {
        $area = DB::table('areas')
            ->where('id', $areaId)
            ->first();

        if (!$area) {
            abort(404, 'Area not found.');
        }

        $allAreas = $request->query('all_areas') == 1;
        $from = $request->query('from');
        $to = $request->query('to');
        $filterAreaId = $request->query('filter_area_id', $areaId);

        if ($allAreas) {
            $areaIds = DB::table('areas')->pluck('id')->toArray();

            $area = (object)[
                'areas_name' => 'All Areas',
                'location_name' => 'All Locations',
                'area_name' => 'All Areas'
            ];
        } else {
            $selectedArea = DB::table('areas')
                ->where('id', $filterAreaId)
                ->first();

            if (!$selectedArea) {
                abort(404, 'Selected area not found.');
            }

            $areaIds = [$selectedArea->id];
            $area = $selectedArea;
            $area->area_name = $area->areas_name;
        }

        $references = DB::table('clients_payments as cp')
            ->select(
                'cp.reference_number',
                'cp.due_date',
                DB::raw('MAX(cp.collected_by) as collected_by'),
                DB::raw('COUNT(DISTINCT cp.client_id) as total_clients'),
                DB::raw('SUM(cp.daily) as total_daily_collectibles'),
                DB::raw('SUM(cp.collection) as total_collections')
            )
            ->whereIn('cp.client_area', $areaIds)
            ->when($from && $to, function ($query) use ($from, $to) {
                return $query->whereBetween('cp.due_date', [$from, $to]);
            })
            ->groupBy('cp.reference_number', 'cp.due_date')
            ->orderBy('cp.due_date', 'desc')
            ->get();

        $references = $references->map(function ($ref) use ($areaIds) {
            $loans = DB::table('clients_loans as cl')
                ->join('clients as c', 'cl.client_id', '=', 'c.id')
                ->whereIn('c.area_id', $areaIds)
                ->whereDate('cl.loan_from', '<=', $ref->due_date)
                ->select('cl.*', 'c.id as client_id')
                ->get();

            $payments = DB::table('clients_payments')
                ->where('reference_number', $ref->reference_number)
                ->whereIn('client_id', $loans->pluck('client_id'))
                ->get()
                ->keyBy('client_id');

            $filteredClients = $loans->filter(function ($loan) use ($payments) {
                $balance = $loan->balance ?? 0;
                $payment = $payments[$loan->client_id] ?? null;

                return $balance > 0 || ($balance <= 0 && $payment && ($payment->collection ?? 0) > 0);
            });

            $ref->total_clients = $filteredClients->count();
            $ref->total_daily_collectibles = $filteredClients->sum(function ($loan) {
                return $loan->daily ?? 0;
            });
            $ref->total_collections = $filteredClients->sum(function ($loan) use ($payments) {
                $payment = $payments[$loan->client_id] ?? null;
                return $payment ? ($payment->collection ?? 0) : 0;
            });

            $collector = DB::table('collectors')->where('id', $ref->collected_by)->first();
            $ref->collected_by_name = $collector ? $collector->fullname : 'N/A';

            $ref->cash_count = DB::table('clients_payments')
                ->where('reference_number', $ref->reference_number)
                ->where('type', 'cash')
                ->count();

            $ref->advance_count = DB::table('clients_payments')
                ->where('reference_number', $ref->reference_number)
                ->where('type', 'advance')
                ->count();

            $ref->gcash_count = DB::table('clients_payments')
                ->where('reference_number', $ref->reference_number)
                ->where('type', 'gcash')
                ->count();

            $ref->cheque_count = DB::table('clients_payments')
                ->where('reference_number', $ref->reference_number)
                ->where('type', 'cheque')
                ->count();

            $ref->no_payment_count = DB::table('clients_payments')
                ->where('reference_number', $ref->reference_number)
                ->where('is_collected', 0)
                ->count();

            $ref->total_accounts = $ref->total_clients;
            $ref->active_amount = $ref->total_daily_collectibles;
            $ref->total_collection = $ref->total_collections;
            $ref->collected_by = $ref->collected_by_name;

            return $ref;
        });

        if (!$from || !$to) {
            $from = $references->min('due_date') ? $references->min('due_date') : now()->format('Y-m-d');
            $to = $references->max('due_date') ? $references->max('due_date') : now()->format('Y-m-d');
        }

        $location_name = $area->location_name;
        $areas_name = $area->areas_name;

        return view('admin.areas.print.print_summary_collection', [
            'payments' => $references,
            'area' => $area,
            'from' => $from,
            'to' => $to,
            'location_name' => $location_name,
            'areas_name' => $areas_name
        ]);
    }
}
