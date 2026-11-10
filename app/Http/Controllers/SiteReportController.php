<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Pdf\SiteMonthlyPdfService;
use App\Services\Pdf\NisekoMonthlyPdfService;
use App\Models\Client;
use App\Services\Pdf\TruckMonthlyPdfService;
use App\Models\WorkType;
use App\Models\SiteMonthlyUnitPrice;
use App\Models\ClientMonthlyUnitPrice;

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

    public function niseko(Request $request)
    {
        return view(
            'site_reports.niseko',
            $this->getNisekoData($request)
        );
    }

    public function truck(Request $request)
    {
        return view(
            'site_reports.truck',
            $this->getTruckData($request)
        );
    }

    public function nisekoPdf(
        Request $request,
        NisekoMonthlyPdfService $pdf
    ) {
        return $pdf->preview(
            $this->getNisekoData($request)
        );
    }

    public function nisekoDownload(
        Request $request,
        NisekoMonthlyPdfService $pdf
    ) {
        return $pdf->downloadPdf(
            $this->getNisekoData($request)
        );
    }

    public function truckPdf(
        Request $request,
        TruckMonthlyPdfService $pdf
    ) {
        return $pdf->preview(
            $this->getTruckData($request)
        );
    }

    public function truckDownload(
        Request $request,
        TruckMonthlyPdfService $pdf
    ) {
        return $pdf->downloadPdf(
            $this->getTruckData($request)
        );
    }

    private function getMonthlyReportData(Request $request)
    {


        $month = $request->month
            ? Carbon::parse($request->month . '-01')
            : now();

        $sites = Site::activeOn($month)
            ->orderBy('name')
            ->get();

        $siteId = $request->site_id;

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
                'ユニック',
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

    private function getNisekoData(Request $request)
    {

        if (!$request->filled('month')) {

            return [
                'month' => now(),
                'client' => Client::where('name', 'ニセコ環境（木造解体）')->first(),
                'visibleSites' => collect(),
                'rows' => [],
                'siteTotals' => [],
                'totalManHours' => 0,
                'totalSales' => 0,
                'totalTransportation' => 0,
                'showTable' => false,
            ];
        }
        $month = $request->month
            ? Carbon::parse($request->month . '-01')
            : now()->startOfMonth();

        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();

        $client = Client::where('name', 'ニセコ環境（木造解体）')
            ->firstOrFail();

        /*
    |--------------------------------------------------------------------------
    | 契約期間中の現場
    |--------------------------------------------------------------------------
    */

        $sites = Site::where('client_id', $client->id)
            ->activeOn($month)
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | 日報取得
    |--------------------------------------------------------------------------
    */

        $reports = DailyReport::with([
            'site',
            'details',
        ])
            ->whereBetween('work_date', [$start, $end])
            ->whereIn('site_id', $sites->pluck('id'))
            ->orderBy('work_date')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | 日付一覧
    |--------------------------------------------------------------------------
    */

        $dates = collect();

        for ($date = $start->copy(); $date <= $end; $date->addDay()) {

            $dates->push($date->copy());
        }

        /*
    |--------------------------------------------------------------------------
    | 初期化
    |--------------------------------------------------------------------------
    */

        $rows = [];

        $siteTotals = [];

        foreach ($sites as $site) {

            $siteTotals[$site->id] = 0;
        }

        $totalManHours = 0;
        $totalSales = 0;
        $totalTransportation = 0;

        /*
    |--------------------------------------------------------------------------
    | 1日ごとの行作成
    |--------------------------------------------------------------------------
    */

        foreach ($dates as $date) {

            $dateKey = $date->format('Y-m-d');

            $dayReports = $reports->where(
                fn($r) => $r->work_date->format('Y-m-d') == $dateKey
            );

            $row = [

                'date' => $date,

                'sites' => [],

                'total_man' => 0,

                'sales' => 0,

                'transportation' => 0,

            ];

            foreach ($dayReports as $report) {

                $man = $report->details->sum('man_hours');

                $sales = $report->details->sum('sales');

                $transportation = $report->details->sum('transportation_cost');

                $row['sites'][$report->site_id] = [

                    'man' => $man,

                    'sales' => $sales,

                ];

                $row['total_man'] += $man;

                $row['sales'] += $sales;

                $row['transportation'] += $transportation;

                $siteTotals[$report->site_id] += $man;

                $totalManHours += $man;

                $totalSales += $sales;

                $totalTransportation += $transportation;
            }

            $rows[] = $row;
        }

        /*
    |--------------------------------------------------------------------------
    | 作業があった現場だけ表示
    |--------------------------------------------------------------------------
    */

        $visibleSites = $sites->filter(function ($site) use ($rows) {

            foreach ($rows as $row) {

                if (isset($row['sites'][$site->id])) {

                    return true;
                }
            }

            return false;
        })->values();

        return [
            'showTable' => true,

            'client' => $client,

            'month' => $month,

            'visibleSites' => $visibleSites,

            'rows' => $rows,

            'siteTotals' => $siteTotals,

            'totalManHours' => $totalManHours,

            'totalSales' => $totalSales,

            'totalTransportation' => $totalTransportation,

        ];
    }


    private function getTruckData(Request $request)
    {
        if (!$request->filled('month')) {
            return [
                'month' => now(),
                'visibleSites' => collect(),
                'rows' => [],
                'siteTotals' => [],
                'totalManHours' => 0,
                'totalSales' => 0,
                'totalTransportation' => 0,
                'showTable' => false,
            ];
        }

        $month = Carbon::parse($request->month . '-01');

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        /*
    |--------------------------------------------------------------------------
    | 北海道リサイクラーのトラック現場
    |--------------------------------------------------------------------------
    */

        $sites = Site::where('name', 'like', '%(トラック)%')
            ->whereHas('client', function ($query) {
                $query->where(
                    'name',
                    'like',
                    '%北海道リサイクラー%'
                );
            })
            ->activeOn($month)
            ->with('client')
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | 日報取得
    |--------------------------------------------------------------------------
    */

        $reports = DailyReport::with([
            'site.client',
            'details.workType',
        ])
            ->whereBetween('work_date', [$start, $end])
            ->whereIn('site_id', $sites->pluck('id'))
            ->orderBy('work_date')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | 日付一覧
    |--------------------------------------------------------------------------
    */

        $dates = collect();

        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $dates->push($date->copy());
        }

        /*
    |--------------------------------------------------------------------------
    | 初期化
    |--------------------------------------------------------------------------
    */

        $rows = [];
        $siteTotals = [];

        foreach ($sites as $site) {
            $siteTotals[$site->id] = 0;
        }

        $totalManHours = 0;
        $totalSales = 0;
        $totalExpressway = 0;
        $totalOvertime = 0;

        /*
    |--------------------------------------------------------------------------
    | 日付ごとの集計
    |--------------------------------------------------------------------------
    */

        foreach ($dates as $date) {

            $dateKey = $date->format('Y-m-d');

            $dayReports = $reports->filter(function ($report) use ($dateKey) {
                return $report->work_date->format('Y-m-d') === $dateKey;
            });

            $row = [
                'date' => $date,
                'sites' => [],
                'total_man' => 0,
                'sales' => 0,
                'expressway' => 0,
                'overtime' => 0,
            ];

            foreach ($dayReports as $report) {

                // トラックの作業だけを抽出
                $truckDetails = $report->details->filter(function ($detail) {
                    return $detail->workType
                        && $detail->workType->name === 'トラック';
                });

                $man = $truckDetails->sum('man_hours');

                $sales = $truckDetails->sum('sales');

                $expressway = $truckDetails->sum(
                    'expressway_cost'
                );

                $overtime = $truckDetails->sum(
                    'overtime_hours'
                );

                if (
                    $man <= 0 &&
                    $sales <= 0 &&
                    $expressway <= 0 &&
                    $overtime <= 0
                ) {
                    continue;
                }

                $row['sites'][$report->site_id] = [
                    'man' => $man,
                    'sales' => $sales,
                    'expressway' => $expressway,
                    'overtime' => $overtime,
                ];

                $row['total_man'] += $man;
                $row['sales'] += $sales;
                $row['expressway'] += $expressway;
                $row['overtime'] += $overtime;

                $siteTotals[$report->site_id] += $man;

                $totalManHours += $man;
                $totalSales += $sales;
                $totalExpressway += $expressway;
                $totalOvertime += $overtime;
            }

            $rows[] = $row;
        }

        /*
    |--------------------------------------------------------------------------
    | 作業実績がある現場だけ表示
    |--------------------------------------------------------------------------
    */

        $visibleSites = $sites->filter(function ($site) use ($rows) {

            foreach ($rows as $row) {

                if (isset($row['sites'][$site->id])) {
                    return true;
                }
            }

            return false;
        })->values();

        // 対象年月のトラック単価を取得
        $priceMonth = \Carbon\Carbon::parse($month)
            ->startOfMonth()
            ->toDateString();

        // トラックの作業種別ID
        $truckWorkTypeId = WorkType::where('name', 'トラック')
            ->value('id');

        // 現場ごとに適用単価を設定
        $visibleSites->each(function ($site) use ($priceMonth, $truckWorkTypeId) {

            $site->loadMissing('client');

            $client = $site->client;

            if (!$client || !$truckWorkTypeId) {
                $site->truck_unit_price_for_month = 0;
                return;
            }

            // ① 現場別月別単価
            $sitePrice = SiteMonthlyUnitPrice::where('site_id', $site->id)
                ->where('work_type_id', $truckWorkTypeId)
                ->whereDate('price_month', $priceMonth)
                ->value('unit_price');

            if ($sitePrice !== null) {
                $site->truck_unit_price_for_month = $sitePrice;
                return;
            }

            // ② 取引先別月別単価
            $clientPrice = ClientMonthlyUnitPrice::where('client_id', $client->id)
                ->where('work_type_id', $truckWorkTypeId)
                ->whereDate('price_month', $priceMonth)
                ->value('unit_price');

            if ($clientPrice !== null) {
                $site->truck_unit_price_for_month = $clientPrice;
                return;
            }

            // ③ 取引先の基本単価
            $site->truck_unit_price_for_month = $client->truck_unit_price ?? 0;
        });

        return [
            'showTable' => true,
            'month' => $month,
            'visibleSites' => $visibleSites,
            'rows' => $rows,
            'siteTotals' => $siteTotals,
            'totalManHours' => $totalManHours,
            'totalSales' => $totalSales,
            'totalExpressway' => $totalExpressway,
            'totalOvertime' => $totalOvertime,
        ];
    }
}
