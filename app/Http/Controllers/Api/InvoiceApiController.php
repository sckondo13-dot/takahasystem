<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceApiController extends Controller
{
    public function summary(Request $request)
    {
        $month = Carbon::createFromFormat(
            'Y-m',
            $request->month
        );

        $start = $month->copy()->startOfMonth();

        $end = $month->copy()->endOfMonth();

        $site = Site::findOrFail(
            $request->site_id
        );

        $reports = DailyReport::with('details')
            ->where('site_id', $site->id)
            ->whereBetween(
                'work_date',
                [$start, $end]
            )
            ->get();

        $manHours = 0;

        $sales = 0;

        $transportation = 0;

        foreach ($reports as $report) {

            $manHours +=
                $report->details->sum('man_hours');

            $sales +=
                $report->details->sum('sales');

            $transportation +=
                $report->details->sum(
                    'transportation_cost'
                );
        }

        return response()->json([

            'site' => $site->name,

            'man_hours' => $manHours,

            'unit_price' => $site->client->demolition_unit_price,

            'sales' => $sales,

            'transportation' => $transportation,

        ]);
    }
}
