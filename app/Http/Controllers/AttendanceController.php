<?php

namespace App\Http\Controllers;

use App\Models\DailyReportDetail;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\EmployeeAllowance;
use Spatie\LaravelPdf\Facades\Pdf;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        /**
         * 社員一覧
         */
        $employees = Employee::orderBy('name')->get();

        /**
         * 選択社員
         */
        $employeeId = $request->employee_id;

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

        if ($employeeId) {

            $details = DailyReportDetail::with([
                'dailyReport.site.client',
                'workType',
                'employee',
            ])
                ->where('employee_id', $employeeId)
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

            $fixedAllowances = EmployeeAllowance::with('allowance')
                ->where('employee_id', $employeeId)
                ->whereNull('end_date')
                ->whereHas('allowance', function ($query) {

                    $query->where('type', 'fixed');
                })
                ->get();

            $fixedAllowanceTotal =
                $fixedAllowances->sum('amount');

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

        return view('attendance.index', compact(
            'employees',
            'employeeId',
            'month',
            'details',
            'totalManHours',
            'totalOvertime',
            'totalTransportation',
            'totalExpressway',
            'totalParking',
            'totalWorkAllowance',
            'fixedAllowances',
            'fixedAllowanceTotal',
        ));
    }

    public function pdf(Request $request)
    {
        /**
         * 社員一覧
         */
        $employees = Employee::orderBy('name')->get();

        /**
         * 選択社員
         */
        $employeeId = $request->employee_id;


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

        $employee = null;

        if ($employeeId) {

            $employee = Employee::findOrFail($employeeId);

            $today = now();

            $details = DailyReportDetail::with([
                'dailyReport.site.client',
                'workType',
                'employee',
            ])
                ->where('employee_id', $employeeId)
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

            $fixedAllowances = EmployeeAllowance::with('allowance')
                ->where('employee_id', $employeeId)
                ->whereNull('end_date')
                ->whereHas('allowance', function ($query) {

                    $query->where('type', 'fixed');
                })
                ->get();

            $fixedAllowanceTotal =
                $fixedAllowances->sum('amount');

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

        return Pdf::view(
            'attendance.pdf',
            compact(
                'employee',
                'month',
                'today',
                'details',

                'totalManHours',
                'totalOvertime',

                'totalTransportation',
                'totalExpressway',
                'totalParking',

                'totalWorkAllowance',

                'fixedAllowances',
                'fixedAllowanceTotal',
            )
        )
            ->format('A4')
            ->portrait()
            ->name('個人出勤簿.pdf');
    }
}
