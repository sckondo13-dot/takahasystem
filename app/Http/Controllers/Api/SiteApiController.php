<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SiteApiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('month')) {

            $date = Carbon::createFromFormat(
                'Y-m',
                $request->month
            )->startOfMonth();
        } elseif ($request->filled('date')) {

            $date = Carbon::parse($request->date);
        } else {

            $date = now();
        }

        $sites = Site::activeOn($date)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($sites);
    }
}
