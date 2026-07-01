<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SiteReportController extends Controller
{
    public function index(Request $request)
    {
        $sites = Site::orderBy('name')->get();

        $siteId = $request->site_id;

        $month = $request->month
            ? Carbon::parse($request->month . '-01')
            : now();

        $reports = collect();

        if ($siteId) {

            $reports = DailyReport::with([
                'details.employee',
                'details.subcontractor',
                'details.workType',
                'freeItems'
            ])
                ->where('site_id', $siteId)
                ->whereBetween('work_date', [
                    $month->copy()->startOfMonth(),
                    $month->copy()->endOfMonth()
                ])
                ->orderBy('work_date')
                ->get();
        }
        $totalSales = $reports
            ->flatMap->details
            ->sum('sales');

        $totalTransportation = $reports
            ->flatMap->details
            ->sum('transportation_cost');

        $totalExpressway = $reports
            ->flatMap->details
            ->sum('expressway_cost');

        $totalParking = $reports
            ->flatMap->details
            ->sum('parking_cost');

        $totalManHours = $reports
            ->flatMap->details
            ->sum('man_hours');

        return view(
            'site_reports.index',
            compact(
                'sites',
                'siteId',
                'month',
                'reports',
                'totalSales',
                'totalTransportation',
                'totalExpressway',
                'totalParking',
                'totalManHours',
            )
        );
    }

    public function monthly(Request $request)
    {
        $sites = Site::orderBy('name')->get();

        $siteId = $request->site_id;

        $month = $request->month
            ? Carbon::parse($request->month . '-01')
            : now();

        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();

        $dates = collect();
        $reportMap = [];

        $site = null;

        $totalSales = 0;
        $totalManHours = 0;
        $totalTransportation = 0;
        $totalExpressway = 0;
        $totalParking = 0;
        $totalOvertime = 0;

        if ($siteId) {

            $site = Site::with('client')->findOrFail($siteId);

            for ($date = $start->copy(); $date <= $end; $date->addDay()) {
                $dates->push($date->copy());
            }

            $reports = DailyReport::with([
                'details.workType',
                'items'
            ])
                ->where('site_id', $siteId)
                ->whereBetween('work_date', [$start, $end])
                ->orderBy('work_date')
                ->get();

            foreach ($reports as $report) {

                $key = $report->work_date->format('Y-m-d');

                $row = [];

                foreach (
                    [
                        '解体工',
                        '重機',
                        '重機２',
                        'ガス工',
                        'はつり',
                        '石綿',
                        'トラック'
                    ] as $type
                ) {

                    $row[$type] = $report->details
                        ->where('workType.name', $type)
                        ->sum('man_hours');
                }

                $row['total_man'] =
                    $report->details->sum('man_hours');

                $row['sales'] =
                    $report->details->sum('sales');

                $row['transportation'] =
                    $report->details->sum('transportation_cost');

                $row['expressway'] =
                    $report->details->sum('expressway_cost');

                $row['parking'] =
                    $report->details->sum('parking_cost');

                $row['items'] =
                    $report->items;

                $row['overtime'] =
                    $report->details->sum('overtime_hours');

                $row['transportation'] =
                    $report->details->sum('transportation_cost');

                $row['expressway'] =
                    $report->details->sum('expressway_cost');

                $row['parking'] =
                    $report->details->sum('parking_cost');

                $reportMap[$key] = $row;

                $totalSales += $row['sales'];
                $totalManHours += $row['total_man'];
                $totalTransportation += $row['transportation'];
                $totalExpressway += $row['expressway'];
                $totalParking += $row['parking'];
                $totalOvertime += $row['overtime'];
                $totalTransportation += $row['transportation'];
                $totalExpressway += $row['expressway'];
                $totalParking += $row['parking'];
            }
        }

        return view('site_reports.monthly', compact(
            'sites',
            'siteId',
            'month',
            'site',
            'dates',
            'reportMap',
            'totalSales',
            'totalManHours',
            'totalTransportation',
            'totalExpressway',
            'totalParking',
            'totalOvertime',
        ));
    }
}
