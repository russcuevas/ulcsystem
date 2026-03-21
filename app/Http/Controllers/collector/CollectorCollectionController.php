<?php

namespace App\Http\Controllers\collector;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CollectorCollectionController extends Controller
{
    public function CollectorCollectionPage()
    {
        $user = Session::get('user');
        $collectorId = $user->id;

        $areas = DB::table('areas')
            ->where('collector_id', $collectorId)
            ->get(['id', 'location_name']);

        $areaIds = $areas->pluck('id')->toArray();

        $clients = DB::table('clients as c')
            ->whereIn('c.area_id', $areaIds)
            ->get();

        $clientIds = $clients->pluck('id')->toArray();

        $loans = DB::table('clients_loans')
            ->whereIn('client_id', $clientIds)
            ->orderByDesc('id')
            ->get()
            ->groupBy('client_id')
            ->map(fn($loans) => $loans->first());

        $clients = $clients->map(function ($client) use ($areas, $loans) {
            $area = $areas->firstWhere('id', $client->area_id);
            $loan = $loans[$client->id] ?? null;

            return (object) [
                'id' => $client->id,
                'fullname' => $client->fullname,
                'area_id' => $client->area_id,
                'location_name' => $area->location_name ?? 'N/A',
                'loan' => $loan
            ];
        });

        return view('collector.collection.index', compact('clients'));
    }
}
