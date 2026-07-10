<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Pdf\SiteMonthlyPdfService;

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
        return view(
            'site_reports.monthly',
            $this->getMonthlyReportData($request)
        );
    }

    public function monthlyPdf(
        Request $request,
        SiteMonthlyPdfService $pdf
    ) {
        return $pdf->preview(
            $this->getMonthlyReportData($request)
        );
    }

    public function monthlyDownload(
        Request $request,
        SiteMonthlyPdfService $pdf
    ) {
        return $pdf->downloadPdf(
            $this->getMonthlyReportData($request)
        );
    }

    private function getMonthlyReportData(Request $request)
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

        $itemList = collect();
        $itemTotals = [];

        $totalSales = 0;
        $totalManHours = 0;
        $totalTransportation = 0;
        $totalExpressway = 0;
        $totalParking = 0;
        $totalOvertime = 0;

        $visibleTypes = [];
        $typeTotals = [];
        $typeSalesTotals = [];
        $unitPrices = [];

        if ($siteId) {

            $site = Site::with('client')->findOrFail($siteId);

            $workTypes = [
                '解体工',
                '重機',
                '重機２',
                'ガス工',
                'はつり',
                '石綿',
                'トラック',
            ];

            foreach ($workTypes as $type) {
                $typeTotals[$type] = 0;
                $typeSalesTotals[$type] = 0;
            }

            $unitPrices = [
                '解体工'   => $site->client->demolition_unit_price,
                '重機'     => $site->client->heavy_equipment_unit_price,
                '重機２'   => $site->client->heavy_equipment2_unit_price,
                'ガス工'   => 0,
                'はつり'   => $site->client->chipping_unit_price,
                '石綿'     => $site->client->asbestos_unit_price,
                'トラック' => $site->client->truck_unit_price,
            ];

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

                $dateKey = $report->work_date->format('Y-m-d');

                $row = [];

                foreach ($workTypes as $type) {

                    $hours = $report->details
                        ->where('workType.name', $type)
                        ->sum('man_hours');

                    $row[$type] = $hours;

                    $typeTotals[$type] += $hours;

                    $typeSalesTotals[$type] +=
                        $hours * ($unitPrices[$type] ?? 0);
                }

                $row['total_man'] = $report->details->sum('man_hours');
                $row['sales'] = $report->details->sum('sales');
                $row['overtime'] = $report->details->sum('overtime_hours');
                $row['transportation'] = $report->details->sum('transportation_cost');
                $row['expressway'] = $report->details->sum('expressway_cost');
                $row['parking'] = $report->details->sum('parking_cost');
                $row['items'] = $report->items;

                foreach ($report->items as $item) {

                    $itemList->push([
                        'date' => $report->work_date,
                        'name' => $item->name,
                        'quantity' => $item->quantity,
                        'unit' => $item->unit,
                    ]);

                    $itemKey = $item->name . '_' . $item->unit;

                    if (!isset($itemTotals[$itemKey])) {

                        $itemTotals[$itemKey] = [
                            'name' => $item->name,
                            'unit' => $item->unit,
                            'quantity' => 0,
                        ];
                    }

                    $itemTotals[$itemKey]['quantity'] += $item->quantity;
                }

                $reportMap[$dateKey] = $row;

                $totalSales += $row['sales'];
                $totalManHours += $row['total_man'];
                $totalOvertime += $row['overtime'];
                $totalTransportation += $row['transportation'];
                $totalExpressway += $row['expressway'];
                $totalParking += $row['parking'];
            }

            foreach ($typeTotals as $type => $total) {

                if ($total > 0) {
                    $visibleTypes[] = $type;
                }
            }
        }

        return compact(
            'sites',
            'siteId',
            'month',
            'site',
            'dates',
            'reportMap',

            'visibleTypes',
            'typeTotals',
            'unitPrices',
            'typeSalesTotals',

            'totalSales',
            'totalManHours',
            'totalTransportation',
            'totalExpressway',
            'totalParking',
            'totalOvertime',

            'itemList',
            'itemTotals',
        );
    }
}
