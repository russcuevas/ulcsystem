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
        $secretary = Session::get('user');
        if (!$secretary) {
            return redirect('/login')->with('error', 'Please login first');
        }

        $secretary_id = $secretary->id;

        // Get area
        $area = DB::table('areas')
            ->where('id', $areaId)
            ->where('secretary_id', $secretary_id)
            ->first();

        if (!$area) {
            return redirect()->route('secretary.areas.page')
                ->with('error', 'You are not authorized to access this area.');
        }

        // Get references
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

        // Count total clients per reference (filtered like CollectionDetailPage)
        $references = $references->map(function ($ref) use ($areaId) {

            // Get all loans started on or before due date
            $loans = DB::table('clients_loans as cl')
                ->join('clients as c', 'cl.client_id', '=', 'c.id')
                ->where('c.area_id', $areaId)
                ->whereDate('cl.loan_from', '<=', $ref->due_date)
                ->select('cl.*', 'c.id as client_id')
                ->get();

            // Get payments for this reference
            $payments = DB::table('clients_payments')
                ->where('client_area', $areaId)
                ->where('reference_number', $ref->reference_number)
                ->get()
                ->keyBy('client_id');

            // Filter loans like in CollectionDetailPage
            $filteredClients = $loans->filter(function ($loan) use ($payments) {
                $balance = $loan->balance ?? 0;
                $payment = $payments[$loan->client_id] ?? null;

                return $balance > 0 || ($balance <= 0 && $payment && ($payment->collection ?? 0) > 0);
            });

            $ref->total_clients = $filteredClients->count();

            return $ref;
        });

        return view('secretary.areas.collections_references', [
            'references' => $references,
            'areaId' => $areaId,
            'location_name' => $area->location_name ?? 'N/A',
            'areas_name' => $area->areas_name ?? 'N/A'
        ]);
    }

    public function CollectionDetailPage($referenceNumber)
    {
        $secretary = Session::get('user');
        if (!$secretary) {
            return redirect('/login')->with('error', 'Please login first');
        }

        $secretary_id = $secretary->id;

        $reference = DB::table('clients_payments')
            ->where('reference_number', $referenceNumber)
            ->first();

        if (!$reference) {
            return redirect()->route('secretary.areas.page')
                ->with('error', 'Reference not found.');
        }

        $area = DB::table('areas')
            ->where('id', $reference->client_area)
            ->where('secretary_id', $secretary_id)
            ->first();

        if (!$area) {
            return redirect()->route('secretary.areas.page')
                ->with('error', 'Unauthorized.');
        }

        $selectedDate = $reference->due_date;

        // ✅ REMOVE balance filter
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

        // Payments
        $payments = DB::table('clients_payments')
            ->where('reference_number', $referenceNumber)
            ->get()
            ->keyBy('client_id');

        // Combine
        // After you combine loans and payments
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

        // ✅ Filter: hide fully paid loans that have no payment
        $clients = $clients->filter(function ($c) {
            $balance = $c->loan->balance ?? 0;

            // Show if:
            // 1. Balance > 0 (still owed)
            // 2. OR Balance = 0 **but there is a payment record**
            return $balance > 0 || ($balance <= 0 && $c->payment && ($c->payment->collection ?? 0) > 0);
        })->values(); // reset keys

        $totalClients = $clients->count();

        $totalCollections = $clients->sum(function ($c) {
            return $c->payment->collection ?? 0;
        });

        $totalDailyCollectibles = $clients->sum(function ($c) {
            return $c->loan->daily ?? 0;
        });

        return view('secretary.areas.collection_detail', [
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

    public function collectPayment(Request $request, $refNo)
    {
        $secretary = Session::get('user');
        if (!$secretary) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $action = $request->input('action');

        // Get reference to find area and date
        $reference = DB::table('clients_payments')->where('reference_number', $refNo)->first();
        if (!$reference) {
            return response()->json(['message' => 'Reference not found'], 404);
        }

        $selectedDate = $reference->due_date;
        $areaId = $reference->client_area;

        // Get collector for this area
        $collector = DB::table('areas')
            ->where('id', $areaId)
            ->value('collector_id');

        // -----------------------------
        // Fetch loans depending on action
        // -----------------------------
        if ($action === 'no_payment') {
            // Include all loans with balance > 0, including lapsed loans
            $loans = DB::table('clients_loans as cl')
                ->join('clients as c', 'cl.client_id', '=', 'c.id')
                ->where('c.area_id', $areaId)
                ->where('cl.balance', '>', 0)
                ->select('cl.*', 'c.id as client_id')
                ->get();
        } else {
            // Only active loans for "collect"
            // All loans with balance that already started (include lapsed, exclude future)
            $loans = DB::table('clients_loans as cl')
                ->join('clients as c', 'cl.client_id', '=', 'c.id')
                ->where('c.area_id', $areaId)
                ->where('cl.balance', '>', 0)
                ->whereDate('cl.loan_from', '<=', $selectedDate) // only started loans
                ->select('cl.*', 'c.id as client_id')
                ->get();
        }

        // Fetch all existing payments for this reference in one query
        $payments = DB::table('clients_payments')
            ->where('reference_number', $refNo)
            ->whereIn('client_id', $loans->pluck('client_id'))
            ->get()
            ->keyBy('client_id'); // quick lookup

        foreach ($loans as $loan) {
            $payment = $payments[$loan->client_id] ?? null;

            // -----------------------------
            // Handle "No Payment"
            // -----------------------------
            if ($action === 'no_payment') {
                // Only consider loans that already started
                if (\Carbon\Carbon::parse($loan->loan_from)->lte($selectedDate)) {

                    if (!$payment) {
                        // Insert new row for clients with no payment
                        DB::table('clients_payments')->insert([
                            'reference_number' => (string) $refNo,
                            'client_id' => $loan->client_id,
                            'client_loans_id' => $loan->id,
                            'client_area' => $areaId,
                            'collection' => 0,
                            'type' => 'NO PAYMENT',
                            'is_collected' => 0,
                            'due_date' => $selectedDate,
                            'daily' => $loan->daily ?? 0,
                            'old_balance' => $loan->balance ?? 0,
                            'created_by' => $secretary->id,
                            'collected_by' => $collector,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    } else {
                        // Update existing payment if not collected and collection is 0
                        if (($payment->collection === null || $payment->collection == 0) && $payment->type !== 'NO PAYMENT') {
                            DB::table('clients_payments')
                                ->where('id', $payment->id)
                                ->update([
                                    'type' => 'NO PAYMENT',
                                    'is_collected' => 0,
                                    'updated_at' => now()
                                ]);
                        }
                    }
                } // end check for started loans
            }

            // -----------------------------
            // Handle "Collect"
            // -----------------------------
            if ($action === 'collect') {
                // Only update payments that exist, have a collection > 0, and are not yet collected
                if ($payment && $payment->collection > 0 && $payment->is_collected == 0) {

                    // Compute new balance
                    $newBalance = $loan->balance - $payment->collection;
                    $newBalance = max($newBalance, 0); // prevent negative balance

                    // Update payment as collected
                    DB::table('clients_payments')
                        ->where('id', $payment->id)
                        ->update([
                            'is_collected' => 1,
                            'updated_at' => now()
                        ]);

                    // Update loan balance
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
}
