<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\DailyReportDetail;
use App\Models\ClientMonthlyUnitPrice;
use App\Models\SiteMonthlyUnitPrice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlySalesRecalculationController extends Controller
{
    /**
     * 月別売上の再計算画面
     */

    public function index(Request $request)
    {
        $validated = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $month = $validated['month'] ?? now()->format('Y-m');

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $priceMonth = $startDate->toDateString();

        // 対象年月の日報を取得
        $reports = DailyReport::with([
            'site.client',
            'details.workType',
        ])
            ->whereBetween('work_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->orderBy('work_date')
            ->get();

        // 日報件数
        $reportCount = $reports->count();

        // 現場数
        $siteCount = $reports->pluck('site_id')->unique()->count();

        // 日報明細件数
        $detailCount = $reports->sum(function ($report) {
            return $report->details->count();
        });

        // 現場ごとの集計
        $siteSummaries = $reports
            ->groupBy('site_id')
            ->map(function ($siteReports) {
                return [
                    'site_name' => $siteReports->first()->site->name ?? '現場未登録',
                    'report_count' => $siteReports->count(),
                    'detail_count' => $siteReports->sum(function ($report) {
                        return $report->details->count();
                    }),
                ];
            })
            ->values();

        // 再計算前・再計算後の売上合計
        $beforeSales = 0;
        $afterSales = 0;

        foreach ($reports as $report) {
            $site = $report->site;
            $client = $site?->client;

            if (!$site || !$client) {
                continue;
            }

            foreach ($report->details as $detail) {
                $workType = $detail->workType;

                if (!$workType) {
                    continue;
                }

                // 現在保存されている売上
                $beforeSales += (float) ($detail->sales ?? 0);

                // 現在の単価設定を取得
                $unitPrice = $this->getUnitPrice(
                    $site,
                    $client,
                    $workType->id,
                    $workType->name,
                    $priceMonth
                );

                $manHours = (float) $detail->man_hours;
                $overtimeHours = (float) ($detail->overtime_hours ?? 0);

                // 再計算後の売上（予定）
                $afterSales += $this->calculateSales(
                    $unitPrice,
                    $manHours,
                    $overtimeHours
                );
            }
        }

        // 差額
        $salesDifference = $afterSales - $beforeSales;

        return view('monthly-sales-recalculation.index', compact(
            'month',
            'reportCount',
            'siteCount',
            'detailCount',
            'siteSummaries',
            'beforeSales',
            'afterSales',
            'salesDifference'
        ));
    }


    /**
     * 指定年月の売上を一括再計算
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $month = $validated['month'];

        $priceMonth = Carbon::createFromFormat('Y-m', $month)
            ->startOfMonth()
            ->toDateString();

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $updatedCount = 0;

        DB::transaction(function () use (
            $startDate,
            $endDate,
            $priceMonth,
            &$updatedCount
        ) {
            DailyReport::with([
                'site.client',
                'details.workType',
            ])
                ->whereBetween('work_date', [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                ])
                ->chunkById(100, function ($reports) use (
                    $priceMonth,
                    &$updatedCount
                ) {
                    foreach ($reports as $report) {
                        $site = $report->site;
                        $client = $site?->client;

                        if (!$site || !$client) {
                            continue;
                        }

                        foreach ($report->details as $detail) {
                            $workType = $detail->workType;

                            if (!$workType) {
                                continue;
                            }

                            $unitPrice = $this->getUnitPrice(
                                $site,
                                $client,
                                $workType->id,
                                $workType->name,
                                $priceMonth
                            );

                            $manHours = (float) $detail->man_hours;
                            $overtimeHours = (float) ($detail->overtime_hours ?? 0);

                            $sales = $this->calculateSales(
                                $unitPrice,
                                $manHours,
                                $overtimeHours
                            );

                            // 売上だけを更新する
                            DailyReportDetail::whereKey($detail->id)
                                ->update([
                                    'sales' => $sales,
                                ]);

                            $updatedCount++;
                        }
                    }
                });
        });

        return redirect()
            ->route('monthly-sales-recalculation.index', [
                'month' => $month,
            ])
            ->with(
                'success',
                "{$month} の売上を {$updatedCount} 件再計算しました。"
            );
    }

    /**
     * 単価を取得する
     *
     * 優先順位：
     * 1. 現場別月別単価
     * 2. 取引先別月別単価
     * 3. 取引先の基本単価
     */
    private function getUnitPrice(
        $site,
        $client,
        int $workTypeId,
        string $workTypeName,
        string $priceMonth
    ): int {
        $sitePrice = SiteMonthlyUnitPrice::where('site_id', $site->id)
            ->where('work_type_id', $workTypeId)
            ->whereDate('price_month', $priceMonth)
            ->value('unit_price');

        if ($sitePrice !== null) {
            return (int) $sitePrice;
        }

        $clientPrice = ClientMonthlyUnitPrice::where('client_id', $client->id)
            ->where('work_type_id', $workTypeId)
            ->whereDate('price_month', $priceMonth)
            ->value('unit_price');

        if ($clientPrice !== null) {
            return (int) $clientPrice;
        }

        $basePriceColumn = match ($workTypeName) {
            '解体工' => 'demolition_unit_price',
            '重機' => 'heavy_equipment_unit_price',
            '重機2' => 'heavy_equipment2_unit_price',
            'はつり' => 'chipping_unit_price',
            '石綿' => 'asbestos_unit_price',
            'トラック' => 'truck_unit_price',
            'ユニック' => 'unic_unit_price',
            default => null,
        };

        if (!$basePriceColumn) {
            return 0;
        }

        return (int) ($client->{$basePriceColumn} ?? 0);
    }

    /**
     * 売上計算
     */
    private function calculateSales(
        float $unitPrice,
        float $manHours,
        float $overtimeHours
    ): float {
        $sales = $unitPrice * $manHours;
        $sales += ($unitPrice / 8) * 1.25 * $overtimeHours;

        return $sales;
    }
}
