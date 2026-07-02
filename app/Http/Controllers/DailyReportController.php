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

        $dailyReport = $this->createDailyReport($request);

        $duplicateWorkers = $this->saveWorkers(
            $request,
            $dailyReport
        );

        $this->saveItems(
            $request,
            $dailyReport
        );

        $message = '日報を登録しました';

        if (!empty($duplicateWorkers)) {

            $message .= '（一部重複した作業者は登録されませんでした）';
        }

        return redirect()
            ->route('daily-reports.index')
            ->with('success', $message);
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

    public function update(
        Request $request,
        DailyReport $dailyReport
    ) {

        $request->validate([
            'site_id' => 'required',
            'work_date' => 'required|date',
        ]);

        $this->updateDailyReport(
            $request,
            $dailyReport
        );

        $dailyReport->details()->delete();
        $dailyReport->items()->delete();

        $duplicateWorkers = $this->saveWorkers(
            $request,
            $dailyReport
        );

        $this->saveItems(
            $request,
            $dailyReport
        );

        $message = '日報を更新しました';

        if (!empty($duplicateWorkers)) {

            $message .= '（一部重複した作業者は登録されませんでした）';
        }

        return redirect()
            ->route(
                'daily-reports.show',
                $dailyReport
            )
            ->with('success', $message);
    }

    private function createDailyReport(Request $request)
    {
        return DailyReport::firstOrCreate(
            [
                'site_id' => $request->site_id,
                'work_date' => $request->work_date,
            ],
            [
                'note' => $request->note,
            ]
        );
    }

    private function updateDailyReport(
        Request $request,
        DailyReport $dailyReport
    ) {
        $dailyReport->update([

            'site_id' => $request->site_id,

            'work_date' => $request->work_date,

            'note' => $request->note,

        ]);
    }

    private function saveWorkers(
        Request $request,
        DailyReport $dailyReport
    ) {

        $duplicateWorkers = [];

        $dailyReport->load('site.client');

        $client = $dailyReport->site->client;

        foreach ($request->worker as $index => $worker) {

            if (!$worker) {
                continue;
            }

            [$type, $id] = explode('_', $worker);

            $employeeId = $type === 'employee'
                ? $id
                : null;

            $subcontractorId = $type === 'subcontractor'
                ? $id
                : null;

            if (
                $this->isDuplicateWorker(
                    $dailyReport,
                    $employeeId,
                    $subcontractorId
                )
            ) {

                $duplicateWorkers[] = $worker;

                continue;
            }

            $this->createDetail(
                $request,
                $dailyReport,
                $client,
                $employeeId,
                $subcontractorId,
                $index
            );
        }

        return $duplicateWorkers;
    }

    private function isDuplicateWorker(
        DailyReport $dailyReport,
        $employeeId,
        $subcontractorId
    ) {

        $query = DailyReportDetail::where(
            'daily_report_id',
            $dailyReport->id
        );

        if ($employeeId) {

            $query->where(
                'employee_id',
                $employeeId
            );
        }

        if ($subcontractorId) {

            $query->where(
                'subcontractor_id',
                $subcontractorId
            );
        }

        return $query->exists();
    }

    private function createDetail(
        Request $request,
        DailyReport $dailyReport,
        $client,
        $employeeId,
        $subcontractorId,
        $index
    ) {

        $attendance = $this->findAttendance(
            $request,
            $index
        );

        $workType = WorkType::find(
            $request->work_type_id[$index]
        );

        $unitPrice = $this->getUnitPrice(
            $client,
            $workType
        );

        $manHours = $request->man_hours[$index];

        $overtimeHours = $request->overtime_hours[$index] ?? 0;

        $sales = $this->calculateSales(
            $unitPrice,
            $manHours,
            $overtimeHours
        );

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
            'attendance_time_name' => $attendance?->name,
            'start_time' => $attendance?->start_time,
            'end_time' => $attendance?->end_time,
        ]);
    }

    private function saveItems(
        Request $request,
        DailyReport $dailyReport
    ) {

        foreach ($request->item_name ?? [] as $index => $name) {

            if (empty($name)) {

                continue;
            }

            $quantity = $request->item_quantity[$index] ?? 1;

            DailyReportItem::create([

                'daily_report_id' => $dailyReport->id,

                'category' => $request->item_category[$index] ?? '貸出',

                'name' => $name,

                'quantity' => $quantity,

                'unit' => $request->item_unit[$index] ?? null,

                'unit_price' => 0,

                'amount' => 0,

                'note' => $request->item_note[$index] ?? null,

            ]);
        }
    }

    private function calculateSales(
        float $unitPrice,
        float $manHours,
        float $overtimeHours
    ): float {

        $sales = $unitPrice * $manHours;

        $sales += ($unitPrice / 8) * 1.25 * $overtimeHours;

        return $sales;
    }

    private function findAttendance(
        Request $request,
        int $index
    ): ?AttendanceTime {

        if (empty($request->attendance_time_id[$index])) {
            return null;
        }

        return AttendanceTime::find(
            $request->attendance_time_id[$index]
        );
    }

    private function getUnitPrice($client, WorkType $workType): int
    {
        return match ($workType->name) {

            '解体工' => $client->demolition_unit_price,

            '重機' => $client->heavy_equipment_unit_price,

            '重機2' => $client->heavy_equipment2_unit_price,

            'はつり' => $client->chipping_unit_price,

            '石綿' => $client->asbestos_unit_price,

            'トラック' => $client->truck_unit_price,

            'ユニック' => $client->unic_unit_price,

            default => 0,
        };
    }
}
