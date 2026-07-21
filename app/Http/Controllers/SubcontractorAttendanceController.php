<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyReportDetail;
use Carbon\Carbon;
use App\Services\Pdf\SubcontractorAttendancePdfService;
use App\Models\Subcontractor;

class SubcontractorAttendanceController extends Controller
{
    public function index(Request $request)
    {
        /**
         * 下請け一覧
         */
       $subcontractors = Subcontractor::orderBy('name')->get();

        /**
         * 選択社員
         */
        $subcontractorId = $request->subcontractor_id;

        /**
         * 月
         */
        $month = $request->month
            ? Carbon::parse($request->month . '-01')
            : now();

        $start = $month->copy()->startOfMonth();

        $end = $month->copy()->endOfMonth();

        /**
         * 明細
         */
        $details = collect();

        /**
         * 合計
         */
        $totalManHours = 0;

        $totalOvertime = 0;

        $totalTransportation = 0;

        $totalExpressway = 0;

        $totalParking = 0;

        $totalWorkAllowance = 0;

        $fixedAllowances = collect();

        $fixedAllowanceTotal = 0;

        if ($subcontractorId) {

            $details = DailyReportDetail::with([
                'dailyReport.site.client',
                'workType',
                'subcontractor',
            ])
                ->where('subcontractor_id', $subcontractorId)
                ->whereHas('dailyReport', function ($query) use ($start, $end) {

                    $query->whereBetween(
                        'work_date',
                        [$start, $end]
                    );
                })
                ->join(
                    'daily_reports',
                    'daily_reports.id',
                    '=',
                    'daily_report_details.daily_report_id'
                )
                ->orderBy('daily_reports.work_date')
                ->select('daily_report_details.*')
                ->get();


            $totalManHours = $details->sum('man_hours');

            $totalOvertime = $details->sum('overtime_hours');

            $totalTransportation =
                $details->sum('transportation_cost');

            $totalExpressway =
                $details->sum('expressway_cost');

            $totalParking =
                $details->sum('parking_cost');

            $totalWorkAllowance =
                $details->sum('work_allowance');
        }

        return view('attendance.subcontractor', compact(
            'subcontractors',
            'subcontractorId',
            'month',
            'details',
            'totalManHours',
            'totalOvertime',
            'totalTransportation',
            'totalExpressway',
            'totalParking',
        ));
    }

    public function pdf(
        Request $request,
        SubcontractorAttendancePdfService $pdf
    ) {
        return $pdf->preview(
            $this->getPdfData($request)
        );
    }

    public function downloadPdf(
        Request $request,
        SubcontractorAttendancePdfService $pdf
    ) {
        return $pdf->downloadPdf(
            $this->getPdfData($request)
        );
    }

    private function getPdfData(Request $request): array
    {
        $subcontractorId = $request->subcontractor_id;

        $month = $request->month
            ? Carbon::parse($request->month . '-01')
            : now();

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $details = collect();

        $totalManHours = 0;
        $totalOvertime = 0;
        $totalTransportation = 0;
        $totalExpressway = 0;
        $totalParking = 0;
        $totalWorkAllowance = 0;

        $fixedAllowances = collect();
        $fixedAllowanceTotal = 0;

        $employee = null;

        $today = now();

        if ($subcontractorId) {

            $employee = Subcontractor::findOrFail($subcontractorId);

            $details = DailyReportDetail::with([
                'dailyReport.site.client',
                'workType',
                'employee',
            ])
                ->where('subcontractor_id', $subcontractorId)
                ->whereHas('dailyReport', function ($query) use ($start, $end) {
                    $query->whereBetween('work_date', [$start, $end]);
                })
                ->join(
                    'daily_reports',
                    'daily_reports.id',
                    '=',
                    'daily_report_details.daily_report_id'
                )
                ->orderBy('daily_reports.work_date')
                ->select('daily_report_details.*')
                ->get();

            $totalManHours = $details->sum('man_hours');
            $totalOvertime = $details->sum('overtime_hours');
            $totalTransportation = $details->sum('transportation_cost');
            $totalExpressway = $details->sum('expressway_cost');
            $totalParking = $details->sum('parking_cost');
        }

        return compact(
            'subcontractors',
            'month',
            'today',
            'details',
            'totalManHours',
            'totalOvertime',
            'totalTransportation',
            'totalExpressway',
            'totalParking',
        );
    }
}
