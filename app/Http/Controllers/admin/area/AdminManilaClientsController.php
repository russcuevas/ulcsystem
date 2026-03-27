<?php

namespace App\Http\Controllers\admin\area;

use App\Http\Controllers\Controller;
use App\Models\Clients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminManilaClientsController extends Controller
{
    public function AdminManilaClientsPage($id)
    {
        $clients = Clients::where('area_id', $id)->get();

        $area = DB::table('areas')
            ->where('id', $id)
            ->select('areas_name', 'location_name')
            ->first();

        $areas_name = $area->areas_name ?? 'Unknown Area';
        $location_name = $area->location_name ?? 'Unknown Location';

        // Pass the current area ID to the view
        return view('admin.areas.manila.clients', compact('clients', 'areas_name', 'location_name', 'id'));
    }

    public function AdminManilaAddClientRequest(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'fullname'       => 'required|string|max:255',
            'phone'          => 'required|digits:11',
            'address'        => 'required|string|max:255',
            'area_id'        => "required|exists:areas,id|in:$id", // must be exactly the current area ID
            'gender'         => 'required|string',
            'loan_from'      => 'required|date',
            'loan_to'        => 'required|date|after_or_equal:loan_from',
            'loan_amount'    => 'required|numeric|min:1',
            'balance'        => 'required|numeric|min:0',
            'daily'          => 'nullable|numeric|min:0',
            'loan_terms'     => 'required|numeric',
            'pn_number'      => 'required|string|unique:clients_loans,pn_number',
            'release_number' => 'required|string|unique:clients_loans,release_number',
        ]);


        DB::transaction(function () use ($request) {

            $clientId = DB::table('clients')->insertGetId([
                'fullname'   => $request->fullname,
                'phone'      => $request->phone,
                'address'    => $request->address,
                'area_id'    => $request->area_id,
                'gender'     => $request->gender,
                'created_by' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('clients_loans')->insert([
                'client_id'      => $clientId,
                'pn_number'      => $request->pn_number,
                'release_number' => $request->release_number,
                'loan_from'      => $request->loan_from,
                'loan_to'        => $request->loan_to,
                'loan_amount'    => $request->loan_amount,
                'balance'        => $request->balance,
                'daily'          => $request->daily,
                'principal'      => $request->loan_amount,
                'loan_terms'     => $request->loan_terms,
                'loan_status'    => 'new',
                'status' => 'unpaid',
                'created_by'     => 'Admin',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Client added successfully.');
    }

    public function AdminManilaViewClientLoans($id)
    {
        $client = DB::table('clients')
            ->where('id', $id)
            ->first();

        if (!$client) {
            return redirect()->back()->with('error', 'Client not found.');
        }

        $area = DB::table('areas')
            ->where('id', $client->area_id)
            ->select('areas_name', 'location_name')
            ->first();

        $areas_name = $area->areas_name ?? 'Unknown Area';
        $location_name = $area->location_name ?? 'Unknown Location';

        $loans = DB::table('clients_loans')
            ->where('client_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.areas.manila.view_loans', compact('areas_name', 'location_name', 'client', 'loans'));
    }

    public function AdminManilaUpdateClientRequest(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'gender' => 'required|string'
        ]);

        $client = Clients::findOrFail($id);

        $client->update([
            'fullname' => $request->fullname,
            'phone' => $request->phone,
            'address' => $request->address,
            'gender' => $request->gender,
        ]);

        return back()->with('success', 'Client updated successfully!');
    }

    public function AdminManilaSubmitRenewLoan(Request $request, $clientId)
    {
        $request->validate([
            'pn_number'      => 'required|string|unique:clients_loans,pn_number',
            'release_number' => 'required|string|unique:clients_loans,release_number',
            'loan_from'      => 'required|date',
            'loan_to'        => 'required|date|after_or_equal:loan_from',
            'loan_amount'    => 'required|numeric|min:1',
            'balance'        => 'required|numeric|min:0',
            'daily'          => 'required|numeric|min:0',
            'loan_terms'     => 'required|numeric|min:1',
        ]);

        DB::table('clients_loans')->insert([
            'client_id'      => $clientId,
            'pn_number'      => $request->pn_number,
            'release_number' => $request->release_number,
            'loan_from'      => $request->loan_from,
            'loan_to'        => $request->loan_to,
            'loan_amount'    => $request->loan_amount,
            'balance'        => $request->balance,
            'daily'          => $request->daily,
            'principal'      => $request->loan_amount,
            'loan_terms'     => $request->loan_terms,
            'loan_status'    => 'renewal',
            'status'         => 'unpaid',
            'created_by'     => 'Admin',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return redirect()->back()->with('success', 'Loan renewed successfully.');
    }

    public function AdminManilaGenerateSOA($loanId)
    {
        // Get loan
        $loan = DB::table('clients_loans')
            ->select(
                'id',
                'client_id',
                'pn_number',
                'release_number',
                'loan_amount',
                'balance',
                'daily',
                'loan_from',
                'loan_to',
                'loan_terms',
            )
            ->where('id', $loanId)
            ->first();

        if (!$loan) {
            return back()->with('error', 'Loan not found.');
        }

        // Get client with area info
        $client = DB::table('clients')
            ->leftJoin('areas', 'clients.area_id', '=', 'areas.id')
            ->where('clients.id', $loan->client_id)
            ->select(
                'clients.*',
                'areas.location_name',
                'areas.areas_name'
            )
            ->first();

        // Get payments
        $payments = DB::table('clients_payments')
            ->where('client_loans_id', $loanId)
            ->orderBy('due_date', 'asc')
            ->get();


        return view('admin.areas.manila.print.generate_soa', compact(
            'loan',
            'client',
            'payments',
        ));
    }

    public function AdminPrintSummaryLoan($clientId)
    {
        $client = DB::table('clients')
            ->where('id', $clientId)
            ->first();

        if (!$client) {
            return back()->with('error', 'Client not found.');
        }

        $area = DB::table('areas')
            ->where('id', $client->area_id)
            ->first();

        $loans = DB::table('clients_loans')
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalDaily = $loans->sum('daily');
        $totalAmount = $loans->sum('loan_amount');
        $newCount = $loans->where('loan_status', 'new')->count();
        $renewalCount = $loans->where('loan_status', 'renewal')->count();

        return view('admin.areas.print.print_summary_loan', compact(
            'loans',
            'client',
            'area',
            'totalDaily',
            'totalAmount',
            'newCount',
            'renewalCount'
        ));
    }
}
