<?php

namespace App\Http\Controllers;

use App\Models\DailyReportDetail;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        if ($employeeId) {

            $details = DailyReportDetail::with([
                'dailyReport.site',
                'workType',
            ])
                ->where('employee_id', $employeeId)
                ->whereHas('dailyReport', function ($query) use ($start, $end) {

                    $query->whereBetween('work_date', [$start, $end]);
                })
                ->get();

            $totalManHours = $details->sum('man_hours');

            $totalOvertime = $details->sum('overtime_hours');
        }

        return view('attendance.index', compact(
            'employees',
            'employeeId',
            'month',
            'details',
            'totalManHours',
            'totalOvertime',
        ));
    }
}
