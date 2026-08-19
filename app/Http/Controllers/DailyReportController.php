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
use Illuminate\Support\Facades\DB;

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
         * 選択した月に進行中の現場のみ
         */
        $sites = Site::activeOn($month)
            ->orderBy('name')
            ->get();

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

                // 人数
                'count' => $dailyReport->details->count(),

                // 人工合計
                'man_hours' => $dailyReport->details->sum('man_hours'),
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
        $sites = Site::activeAt(now())
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

        /*
    |--------------------------------------------------------------------------
    | 下請の作業内容重複チェック
    |--------------------------------------------------------------------------
    */

        $warnings = $this->checkSubcontractorWorkTypeConflicts($request);

        /*
    |--------------------------------------------------------------------------
    | 確認済みでない場合は登録を止める
    |--------------------------------------------------------------------------
    */

        if (
            !empty($warnings)
            && !$request->boolean('confirm_subcontractor')
        ) {

            return back()
                ->withInput()
                ->with('subcontractor_confirmations', $warnings);
        }

        /*
    |--------------------------------------------------------------------------
    | 日報作成
    |--------------------------------------------------------------------------
    */

        $dailyReport = $this->createDailyReport($request);

        /*
    |--------------------------------------------------------------------------
    | 作業者保存
    |--------------------------------------------------------------------------
    */

        $duplicateWorkers = $this->saveWorkers(
            $request,
            $dailyReport
        );

        /*
    |--------------------------------------------------------------------------
    | 現場費保存
    |--------------------------------------------------------------------------
    */

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

        $sites = Site::activeAt($dailyReport->work_date)
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

            $workTypeId = $request->work_type_id[$index];

            /*
     * 社員：
     * 作業内容に関係なく同じ社員なら重複
     *
     * 下請：
     * 同じ会社＋同じ作業内容なら重複
     */
            if (
                $this->isDuplicateWorker(
                    $dailyReport,
                    $employeeId,
                    $subcontractorId,
                    $request->work_type_id[$index]
                )
            ) {

                $duplicateWorkers[] = $worker;

                continue;
            }

            /*
     * 下請で、
     * 同じ会社だが違う作業内容がすでに存在する場合
     */
            if (
                $subcontractorId &&
                $this->hasDifferentSubcontractorWork(
                    $dailyReport,
                    $subcontractorId,
                    $workTypeId
                )
            ) {

                /*
         * ここは後ほど確認処理を入れる
         */
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
        $subcontractorId,
        $workTypeId
    ) {
        $query = DailyReportDetail::where(
            'daily_report_id',
            $dailyReport->id
        );

        /*
     * 社員
     *
     * 同じ社員が同じ日報に存在したら、
     * 作業内容に関係なく重複
     */
        if ($employeeId) {

            return $query
                ->where('employee_id', $employeeId)
                ->exists();
        }

        /*
     * 下請
     *
     * 同じ会社＋同じ作業内容なら重複
     *
     * 作業内容が違う場合は登録可能
     */
        if ($subcontractorId) {

            return $query
                ->where('subcontractor_id', $subcontractorId)
                ->where('work_type_id', $workTypeId)
                ->exists();
        }

        return false;
    }

    private function hasDifferentSubcontractorWorkType(
        DailyReport $dailyReport,
        $subcontractorId,
        $workTypeId
    ) {
        if (!$subcontractorId) {
            return false;
        }

        return DailyReportDetail::where(
            'daily_report_id',
            $dailyReport->id
        )
            ->where('subcontractor_id', $subcontractorId)
            ->where('work_type_id', '!=', $workTypeId)
            ->exists();
    }

    private function hasDifferentSubcontractorWork(
        DailyReport $dailyReport,
        $subcontractorId,
        $workTypeId
    ) {
        return DailyReportDetail::where(
            'daily_report_id',
            $dailyReport->id
        )
            ->where('subcontractor_id', $subcontractorId)
            ->where('work_type_id', '!=', $workTypeId)
            ->exists();
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

    private function checkSubcontractorWorkTypeConflicts(
        Request $request
    ): array {

        $warnings = [];

        /*
    |--------------------------------------------------------------------------
    | 同じ現場・同じ日の日報を取得
    |--------------------------------------------------------------------------
    */

        $dailyReport = DailyReport::where(
            'site_id',
            $request->site_id
        )
            ->whereDate(
                'work_date',
                $request->work_date
            )
            ->first();

        /*
    |--------------------------------------------------------------------------
    | まだ日報が存在しない場合はチェック不要
    |--------------------------------------------------------------------------
    */

        if (!$dailyReport) {
            return $warnings;
        }

        /*
    |--------------------------------------------------------------------------
    | 既存の明細
    |--------------------------------------------------------------------------
    */

        $existingDetails = DailyReportDetail::with([
            'subcontractor',
            'workType',
        ])
            ->where(
                'daily_report_id',
                $dailyReport->id
            )
            ->whereNotNull('subcontractor_id')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | 今回POSTされた作業者
    |--------------------------------------------------------------------------
    */

        foreach ($request->worker ?? [] as $index => $worker) {

            if (!$worker) {
                continue;
            }

            /*
        |--------------------------------------------------------------------------
        | 下請だけを対象にする
        |--------------------------------------------------------------------------
        */

            if (!str_starts_with($worker, 'subcontractor_')) {
                continue;
            }

            $subcontractorId = str_replace(
                'subcontractor_',
                '',
                $worker
            );

            $workTypeId =
                $request->work_type_id[$index] ?? null;

            if (!$workTypeId) {
                continue;
            }

            /*
        |--------------------------------------------------------------------------
        | 同じ下請会社の既存登録を検索
        |--------------------------------------------------------------------------
        */

            $existing = $existingDetails->first(
                fn($detail) =>
                (int) $detail->subcontractor_id
                    === (int) $subcontractorId
            );

            /*
        |--------------------------------------------------------------------------
        | 既存登録がない場合
        |--------------------------------------------------------------------------
        */

            if (!$existing) {
                continue;
            }

            /*
        |--------------------------------------------------------------------------
        | 同じ作業内容なら通常の重複
        |--------------------------------------------------------------------------
        */

            if (
                (int) $existing->work_type_id
                === (int) $workTypeId
            ) {
                continue;
            }

            /*
        |--------------------------------------------------------------------------
        | 別の作業内容なら確認
        |--------------------------------------------------------------------------
        */

            $newWorkType = WorkType::find($workTypeId);

            $subcontractorName =
                $existing->subcontractor?->name
                ?? '下請会社';

            $existingWorkType =
                $existing->workType?->name
                ?? '作業内容不明';

            $newWorkTypeName =
                $newWorkType?->name
                ?? '作業内容不明';

            $warnings[] = [
                'subcontractor_id' => $subcontractorId,
                'subcontractor_name' => $subcontractorName,
                'existing_work_type' => $existingWorkType,
                'new_work_type' => $newWorkTypeName,
            ];
        }

        return $warnings;
    }

    public function destroy(DailyReport $dailyReport)
    {
        DB::transaction(function () use ($dailyReport) {

            // 作業者明細を削除
            DailyReportDetail::where(
                'daily_report_id',
                $dailyReport->id
            )->delete();

            // 貸出・項目明細を削除
            DailyReportItem::where(
                'daily_report_id',
                $dailyReport->id
            )->delete();

            // 日報本体を削除
            $dailyReport->delete();
        });

        return redirect()
            ->route('daily-reports.index')
            ->with('success', '日報を削除しました。');
    }
}
