<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\DailyReportDetail;
use App\Models\Employee;
use App\Models\Site;
use App\Models\Subcontractor;
use App\Models\WorkType;
use Illuminate\Http\Request;
use App\Models\AttendanceTime;
use App\Models\DailyReportItem;

class DailyReportController extends Controller
{
    /**
     * 一覧
     */
    public function index(Request $request)
    {
        /**
         * 表示月
         */
        $month = $request->month
            ? \Carbon\Carbon::parse($request->month . '-01')
            : now();

        /**
         * 月初・月末
         */
        $start = $month->copy()->startOfMonth();

        $end = $month->copy()->endOfMonth();

        /**
         * 現場
         */
        $sites = Site::orderBy('name')->get();

        /**
         * 日報
         */
        $dailyReports = DailyReport::with('details')
            ->whereBetween('work_date', [$start, $end])
            ->get();

        /**
         * 日付配列
         */
        $dates = [];

        $current = $start->copy();

        while ($current <= $end) {

            $dates[] = $current->copy();

            $current->addDay();
        }

        /**
         * マップ
         */
        $reportMap = [];

        foreach ($dailyReports as $dailyReport) {

            $date = $dailyReport->work_date->format('Y-m-d');

            $reportMap[$date][$dailyReport->site_id] = [

                'report' => $dailyReport,

                'count' => $dailyReport->details->count(),
            ];
        }

        /**
         * 月切替
         */
        $prevMonth = $month->copy()->subMonth()->format('Y-m');

        $nextMonth = $month->copy()->addMonth()->format('Y-m');

        return view('daily_reports.index', compact(
            'sites',
            'dates',
            'reportMap',
            'month',
            'prevMonth',
            'nextMonth',
        ));
    }

    /**
     * 新規画面
     */
    public function create()
    {
        $sites = Site::where('status', '解体中')
            ->orderBy('name')
            ->get();

        $employees = Employee::orderBy('name')->get();

        $subcontractors = Subcontractor::orderBy('name')->get();

        $workTypes = WorkType::orderBy('id')->get();

        $workers = collect();

        $attendanceTimes = AttendanceTime::orderBy('name')->get();

        foreach ($employees as $employee) {

            $workers->push([
                'type' => 'employee',
                'id' => $employee->id,
                'name' => '【社員】' . $employee->name,
            ]);
        }

        foreach ($subcontractors as $subcontractor) {

            $workers->push([
                'type' => 'subcontractor',
                'id' => $subcontractor->id,
                'name' => '【下請】' . $subcontractor->name,
            ]);
        }

        return view('daily_reports.create', compact(
            'sites',
            'workTypes',
            'workers',
            'attendanceTimes',
        ));
    }

    /**
     * 登録
     */
    public function store(Request $request)
    {
        $request->validate([
            'site_id' => 'required',
            'work_date' => 'required|date',
        ]);

        $dailyReport = DailyReport::create([
            'site_id' => $request->site_id,
            'work_date' => $request->work_date,
            'note' => $request->note,
        ]);

        foreach ($request->worker as $index => $worker) {

            if (!$worker) {
                continue;
            }

            $type = explode('_', $worker)[0];

            $id = explode('_', $worker)[1];

            $dailyReport->load('site.client');

            $client = $dailyReport->site->client;

            $employeeId = null;
            $subcontractorId = null;
            $attendance = null;

            if ($type === 'employee') {

                $employeeId = $id;
            } else {

                $subcontractorId = $id;
            }

            $workType = WorkType::find($request->work_type_id[$index]);

            $unitPrice = 0;

            switch ($workType->name) {

                case '解体工':
                    $unitPrice = $client->demolition_unit_price;
                    break;

                case '重機':
                    $unitPrice = $client->heavy_equipment_unit_price;
                    break;

                case '重機2':
                    $unitPrice = $client->heavy_equipment2_unit_price;
                    break;

                case 'はつり':
                    $unitPrice = $client->chipping_unit_price;
                    break;

                case '石綿':
                    $unitPrice = $client->asbestos_unit_price;
                    break;

                case 'トラック':
                    $unitPrice = $client->truck_unit_price;
                    break;

                case 'ユニック':
                    $unitPrice = $client->unic_unit_price;
                    break;
            }

            $manHours = $request->man_hours[$index];

            $overtimeHours = $request->overtime_hours[$index] ?? 0;

            /**
             * 基本人工
             */
            $sales = $unitPrice * $manHours;

            /**
             * 残業
             * 1人工/8*1.25
             */
            $overtimePrice = ($unitPrice / 8) * 1.25;

            $sales += $overtimePrice * $overtimeHours;

            if (!empty($request->attendance_time_id[$index])) {

                $attendance = AttendanceTime::find(
                    $request->attendance_time_id[$index]
                );
            }

            DailyReportDetail::create([

                'daily_report_id' => $dailyReport->id,

                'employee_id' => $employeeId,

                'subcontractor_id' => $subcontractorId,

                'work_type_id' => $request->work_type_id[$index],

                'man_hours' => $request->man_hours[$index],

                'overtime_hours' => $request->overtime_hours[$index] ?? 0,

                'transportation_cost' => $request->transportation_cost[$index] ?? 0,

                'expressway_cost' => $request->expressway_cost[$index] ?? 0,

                'parking_cost' => $request->parking_cost[$index] ?? 0,

                'sales' => $sales,

                'note' => $request->detail_note[$index] ?? null,

                'attendance_time_name'
                => $attendance?->name,

                'start_time'
                => $attendance?->start_time,

                'end_time'
                => $attendance?->end_time,
            ]);
        }
        foreach ($request->item_name ?? [] as $index => $name) {

            if (empty($name)) {
                continue;
            }

            $quantity = $request->item_quantity[$index] ?? 1;
            $unitPrice = 0;

            DailyReportItem::create([

                'daily_report_id' => $dailyReport->id,

                'category' => $request->item_category[$index] ?? '貸出',

                'name' => $name,

                'quantity' => $quantity,

                'unit' => $request->item_unit[$index] ?? null,

                'unit_price' => $unitPrice,

                'amount' => $quantity * $unitPrice,

                'note' => $request->item_note[$index] ?? null,

            ]);
        }

        return redirect()
            ->route('daily-reports.index')
            ->with('success', '日報を登録しました');
    }

    public function show(DailyReport $dailyReport)
    {
        $dailyReport->load([
            'site',
            'details.employee',
            'details.subcontractor',
            'details.workType',
        ]);

        return view('daily_reports.show', compact('dailyReport'));
    }

    public function edit(DailyReport $dailyReport)
    {
        $dailyReport->load('details');

        $sites = Site::where('status', '解体中')
            ->orWhere('id', $dailyReport->site_id)
            ->orderBy('name')
            ->get();
        $employees = Employee::orderBy('name')->get();

        $subcontractors = Subcontractor::orderBy('name')->get();

        $workTypes = WorkType::orderBy('id')->get();

        $workers = collect();
        $attendanceTimes =
            AttendanceTime::orderBy('name')->get();

        $dailyReport->load([
            'details',
            'items',
        ]);

        foreach ($employees as $employee) {

            $workers->push([
                'type' => 'employee',
                'id' => $employee->id,
                'name' => '【社員】' . $employee->name,
            ]);
        }

        foreach ($subcontractors as $subcontractor) {

            $workers->push([
                'type' => 'subcontractor',
                'id' => $subcontractor->id,
                'name' => '【下請】' . $subcontractor->name,
            ]);
        }

        return view('daily_reports.edit', compact(
            'dailyReport',
            'sites',
            'workTypes',
            'workers',
            'attendanceTimes',
        ));
    }

    public function update(Request $request, DailyReport $dailyReport)
    {
        $request->validate([
            'site_id' => 'required',
            'work_date' => 'required|date',
        ]);

        /**
         * 日報更新
         */
        $dailyReport->update([
            'site_id' => $request->site_id,
            'work_date' => $request->work_date,
            'note' => $request->note,
        ]);

        /**
         * 既存明細削除
         */
        $dailyReport->details()->delete();
        $dailyReport->items()->delete();

        $dailyReport->load('site.client');

        $client = $dailyReport->site->client;

        /**
         * 明細再登録
         */
        foreach ($request->worker as $index => $worker) {

            if (!$worker) {
                continue;
            }

            $type = explode('_', $worker)[0];

            $id = explode('_', $worker)[1];

            $employeeId = null;

            $subcontractorId = null;

            if ($type === 'employee') {

                $employeeId = $id;
            } else {

                $subcontractorId = $id;
            }

            $workType = WorkType::find($request->work_type_id[$index]);

            $unitPrice = 0;

            switch ($workType->name) {

                case '解体工':
                    $unitPrice = $client->demolition_unit_price;
                    break;

                case '重機':
                    $unitPrice = $client->heavy_equipment_unit_price;
                    break;

                case '重機２':
                    $unitPrice = $client->heavy_equipment2_unit_price;
                    break;

                case 'はつり':
                    $unitPrice = $client->chipping_unit_price;
                    break;

                case '石綿':
                    $unitPrice = $client->asbestos_unit_price;
                    break;

                case 'トラック':
                    $unitPrice = $client->truck_unit_price;
                    break;

                case 'ユニック':
                    $unitPrice = $client->unic_unit_price;
                    break;
            }

            $manHours = $request->man_hours[$index];

            $overtimeHours = $request->overtime_hours[$index] ?? 0;

            $sales = $unitPrice * $manHours;

            $overtimePrice = ($unitPrice / 8) * 1.25;

            $sales += $overtimePrice * $overtimeHours;

            $attendance = null;

            if (!empty($request->attendance_time_id[$index])) {

                $attendance = AttendanceTime::find(
                    $request->attendance_time_id[$index]
                );
            }

            DailyReportDetail::create([

                'daily_report_id' => $dailyReport->id,

                'employee_id' => $employeeId,

                'subcontractor_id' => $subcontractorId,

                'work_type_id' => $request->work_type_id[$index],

                'man_hours' => $manHours,

                'overtime_hours' => $overtimeHours,

                'transportation_cost' => $request->transportation_cost[$index] ?? 0,

                'expressway_cost' => $request->expressway_cost[$index] ?? 0,

                'parking_cost' => $request->parking_cost[$index] ?? 0,

                'sales' => $sales,

                'note' => $request->detail_note[$index] ?? null,

                'attendance_time_name'
                => $attendance?->name,

                'start_time'
                => $attendance?->start_time,

                'end_time'
                => $attendance?->end_time,
            ]);
        }

        foreach ($request->item_name ?? [] as $index => $name) {

            if (!$name) {
                continue;
            }

            DailyReportItem::create([

                'daily_report_id' => $dailyReport->id,

                'category' => $request->item_category[$index],

                'name' => $name,

                'quantity' => $request->item_quantity[$index],

                'unit' => $request->item_unit[$index],

            ]);
        }

        return redirect()
            ->route('daily-reports.show', $dailyReport)
            ->with('success', '日報を更新しました');
    }
}
