<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\DailyReportDetail;
use App\Models\Employee;
use App\Models\Site;
use App\Models\Subcontractor;
use App\Models\WorkType;
use App\Models\AttendanceTime;
use App\Models\DailyReportItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyReportController extends Controller
{
    /**
     * 一覧
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 表示月
        |--------------------------------------------------------------------------
        */

        $month = $request->month
            ? Carbon::parse($request->month . '-01')
            : now();

        /*
        |--------------------------------------------------------------------------
        | 月初・月末
        |--------------------------------------------------------------------------
        */

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | 現場
        |--------------------------------------------------------------------------
        | 選択した月に進行中の現場のみ
        */

        $sites = Site::activeOn($month)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 日報
        |--------------------------------------------------------------------------
        */

        $dailyReports = DailyReport::with('details')
            ->whereBetween('work_date', [$start, $end])
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 日付配列
        |--------------------------------------------------------------------------
        */

        $dates = [];

        $current = $start->copy();

        while ($current <= $end) {

            $dates[] = $current->copy();

            $current->addDay();
        }

        /*
        |--------------------------------------------------------------------------
        | 日報マップ
        |--------------------------------------------------------------------------
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

        /*
        |--------------------------------------------------------------------------
        | 月切替
        |--------------------------------------------------------------------------
        */

        $prevMonth = $month
            ->copy()
            ->subMonth()
            ->format('Y-m');

        $nextMonth = $month
            ->copy()
            ->addMonth()
            ->format('Y-m');

        return view(
            'daily_reports.index',
            compact(
                'sites',
                'dates',
                'reportMap',
                'month',
                'prevMonth',
                'nextMonth'
            )
        );
    }

    /**
     * 新規登録画面
     */
    public function create(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 日報日付
        |--------------------------------------------------------------------------
        | URLから work_date が渡された場合はその日付
        | なければ今日
        */

        $workDate = $request->work_date
            ? Carbon::parse($request->work_date)
            : today();

        /*
        |--------------------------------------------------------------------------
        | 現場
        |--------------------------------------------------------------------------
        */

        $sites = Site::activeAt($workDate)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 日報日付時点で在籍している社員
        |--------------------------------------------------------------------------
        */

        $employees = $this->getEmployedEmployees($workDate);

        /*
        |--------------------------------------------------------------------------
        | 下請
        |--------------------------------------------------------------------------
        */

        $subcontractors = Subcontractor::orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 作業内容
        |--------------------------------------------------------------------------
        */

        $workTypes = WorkType::orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 勤務区分
        |--------------------------------------------------------------------------
        */

        $attendanceTimes = AttendanceTime::orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 作業者プルダウン用
        |--------------------------------------------------------------------------
        */

        $workers = collect();

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

        return view(
            'daily_reports.create',
            compact(
                'sites',
                'workTypes',
                'workers',
                'attendanceTimes',
                'workDate'
            )
        );
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
        |
        | DBに既に存在する場合
        | ＋
        | 今回のフォーム内で複数登録されている場合
        |
        */

        $warnings = $this->checkSubcontractorWorkTypeConflicts(
            $request
        );

        /*
        |--------------------------------------------------------------------------
        | 確認されていない場合は登録を止める
        |--------------------------------------------------------------------------
        */

        if (
            !empty($warnings)
            && !$request->boolean('confirm_subcontractor')
        ) {

            return back()
                ->withInput()
                ->with(
                    'subcontractor_confirmations',
                    $warnings
                );
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

        /*
        |--------------------------------------------------------------------------
        | 完了メッセージ
        |--------------------------------------------------------------------------
        */

        $message = '日報を登録しました';

        if (!empty($duplicateWorkers)) {

            $message .=
                '（一部重複した作業者は登録されませんでした）';
        }

        return redirect()
            ->route('daily-reports.index')
            ->with('success', $message);
    }

    /**
     * 詳細
     */
    public function show(DailyReport $dailyReport)
    {
        $dailyReport->load([
            'site',
            'details.employee',
            'details.subcontractor',
            'details.workType',
        ]);

        return view(
            'daily_reports.show',
            compact('dailyReport')
        );
    }

    /**
     * 編集画面
     */
    public function edit(DailyReport $dailyReport)
    {
        /*
        |--------------------------------------------------------------------------
        | 日報
        |--------------------------------------------------------------------------
        */

        $dailyReport->load([
            'details',
            'items',
        ]);

        $workDate = $dailyReport->work_date;

        /*
        |--------------------------------------------------------------------------
        | 現場
        |--------------------------------------------------------------------------
        */

        $sites = Site::activeAt($workDate)
            ->orWhere('id', $dailyReport->site_id)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 日報日付時点で在籍している社員
        |--------------------------------------------------------------------------
        */

        $employees = $this->getEmployedEmployees(
            $workDate
        );

        /*
        |--------------------------------------------------------------------------
        | 下請
        |--------------------------------------------------------------------------
        */

        $subcontractors = Subcontractor::orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 作業内容
        |--------------------------------------------------------------------------
        */

        $workTypes = WorkType::orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 勤務区分
        |--------------------------------------------------------------------------
        */

        $attendanceTimes = AttendanceTime::orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 作業者プルダウン
        |--------------------------------------------------------------------------
        */

        $workers = collect();

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

        return view(
            'daily_reports.edit',
            compact(
                'dailyReport',
                'sites',
                'workTypes',
                'workers',
                'attendanceTimes'
            )
        );
    }

    /**
     * 更新
     */
    public function update(
        Request $request,
        DailyReport $dailyReport
    ) {

        $request->validate([
            'site_id' => 'required',
            'work_date' => 'required|date',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 日付変更後の社員在籍チェック
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | 下請の作業内容重複チェック
        |--------------------------------------------------------------------------
        |
        | 更新時も
        | DBに存在するもの
        | ＋
        | 今回フォーム内のもの
        |
        | を確認する
        */

        $warnings = $this->checkSubcontractorWorkTypeConflicts(
            $request,
            $dailyReport
        );

        /*
        |--------------------------------------------------------------------------
        | 未確認なら更新を止める
        |--------------------------------------------------------------------------
        */

        if (
            !empty($warnings)
            && !$request->boolean('confirm_subcontractor')
        ) {

            return back()
                ->withInput()
                ->with(
                    'subcontractor_confirmations',
                    $warnings
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 日報本体更新
        |--------------------------------------------------------------------------
        */

        $this->updateDailyReport(
            $request,
            $dailyReport
        );

        /*
        |--------------------------------------------------------------------------
        | 既存明細削除
        |--------------------------------------------------------------------------
        */

        $dailyReport->details()->delete();

        $dailyReport->items()->delete();

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

        /*
        |--------------------------------------------------------------------------
        | 完了メッセージ
        |--------------------------------------------------------------------------
        */

        $message = '日報を更新しました';

        if (!empty($duplicateWorkers)) {

            $message .=
                '（一部重複した作業者は登録されませんでした）';
        }

        return redirect()
            ->route(
                'daily-reports.show',
                $dailyReport
            )
            ->with('success', $message);
    }

    /**
     * 日報作成
     */
    private function createDailyReport(
        Request $request
    ) {
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

    /**
     * 日報更新
     */
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

    /**
     * 在籍中の社員取得
     */
    private function getEmployedEmployees(
        Carbon $workDate
    ) {

        return Employee::whereDate(
            'hire_date',
            '<=',
            $workDate
        )
            ->where(function ($query) use ($workDate) {

                $query
                    ->whereNull('retirement_date')
                    ->orWhereDate(
                        'retirement_date',
                        '>=',
                        $workDate
                    );
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * 作業者保存
     */
    private function saveWorkers(
        Request $request,
        DailyReport $dailyReport
    ) {

        $duplicateWorkers = [];

        $dailyReport->load('site.client');

        $client = $dailyReport->site->client;

        foreach ($request->worker ?? [] as $index => $worker) {

            if (!$worker) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | workerを分解
            |--------------------------------------------------------------------------
            */

            [$type, $id] = explode(
                '_',
                $worker,
                2
            );

            $employeeId = $type === 'employee'
                ? $id
                : null;

            $subcontractorId = $type === 'subcontractor'
                ? $id
                : null;

            $workTypeId =
                $request->work_type_id[$index] ?? null;

            if (!$workTypeId) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | 社員の在籍チェック
            |--------------------------------------------------------------------------
            */

            if ($employeeId) {

                $employee = Employee::find(
                    $employeeId
                );

                if (
                    !$employee ||
                    !$employee->isEmployedAt(
                        $dailyReport->work_date
                    )
                ) {
                    continue;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 重複チェック
            |--------------------------------------------------------------------------
            */

            if (
                $this->isDuplicateWorker(
                    $dailyReport,
                    $employeeId,
                    $subcontractorId,
                    $workTypeId
                )
            ) {

                $duplicateWorkers[] = $worker;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | 明細作成
            |--------------------------------------------------------------------------
            */

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

    /**
     * 作業者重複チェック
     */
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
        |--------------------------------------------------------------------------
        | 社員
        |--------------------------------------------------------------------------
        |
        | 同じ社員が同じ日報に存在したら、
        | 作業内容に関係なく重複
        */

        if ($employeeId) {

            return $query
                ->where(
                    'employee_id',
                    $employeeId
                )
                ->exists();
        }

        /*
        |--------------------------------------------------------------------------
        | 下請
        |--------------------------------------------------------------------------
        |--------------------------------------------------------------------------
        | 同じ会社＋同じ作業内容なら重複
        |
        | 同じ会社でも作業内容が違えば登録可能
        */

        if ($subcontractorId) {

            return $query
                ->where(
                    'subcontractor_id',
                    $subcontractorId
                )
                ->where(
                    'work_type_id',
                    $workTypeId
                )
                ->exists();
        }

        return false;
    }

    /**
     * 下請の作業内容重複確認
     *
     * DBに既存
     * ＋
     * 今回フォーム内
     *
     * の両方を確認する
     */
    private function checkSubcontractorWorkTypeConflicts(
        Request $request,
        ?DailyReport $editingDailyReport = null
    ): array {

        $warnings = [];

        /*
        |--------------------------------------------------------------------------
        | 現在の日報を取得
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
        | 更新時
        |--------------------------------------------------------------------------
        | site_id / work_date が変更されていない場合は
        | 編集対象の日報そのものを使用
        */

        if (
            $editingDailyReport &&
            $editingDailyReport->site_id == $request->site_id &&
            $editingDailyReport->work_date->format('Y-m-d')
                === Carbon::parse($request->work_date)->format('Y-m-d')
        ) {

            $dailyReport = $editingDailyReport;
        }

        /*
        |--------------------------------------------------------------------------
        | DB上の既存明細
        |--------------------------------------------------------------------------
        */

        $existingDetails = collect();

        if ($dailyReport) {

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
        }

        /*
        |--------------------------------------------------------------------------
        | 今回フォーム内の下請を整理
        |--------------------------------------------------------------------------
        */

        $formSubcontractors = [];

        foreach ($request->worker ?? [] as $index => $worker) {

            if (!$worker) {
                continue;
            }

            if (
                !str_starts_with(
                    $worker,
                    'subcontractor_'
                )
            ) {
                continue;
            }

            $subcontractorId = (int) str_replace(
                'subcontractor_',
                '',
                $worker
            );

            $workTypeId =
                $request->work_type_id[$index] ?? null;

            if (!$workTypeId) {
                continue;
            }

            $formSubcontractors[] = [

                'subcontractor_id' =>
                    $subcontractorId,

                'work_type_id' =>
                    (int) $workTypeId,

                'index' => $index,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | DBとの比較
        |--------------------------------------------------------------------------
        */

        foreach ($formSubcontractors as $form) {

            $existingDetailsForCompany =
                $existingDetails->filter(
                    fn($detail) =>
                    (int) $detail->subcontractor_id
                        === $form['subcontractor_id']
                );

            foreach (
                $existingDetailsForCompany
                as $existing
            ) {

                /*
                |--------------------------------------------------------------------------
                | 同じ作業内容
                |--------------------------------------------------------------------------
                |
                | これは通常の重複扱いなので
                | 確認ダイアログは出さない
                */

                if (
                    (int) $existing->work_type_id
                    === $form['work_type_id']
                ) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | 違う作業内容
                |--------------------------------------------------------------------------
                */

                $this->addSubcontractorWarning(
                    $warnings,
                    $existing->subcontractor,
                    $existing->workType,
                    WorkType::find(
                        $form['work_type_id']
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 今回フォーム内の比較
        |--------------------------------------------------------------------------
        |
        | 例：
        |
        | A社・解体
        | A社・石綿
        |
        | のような場合
        */

        $grouped =
            collect($formSubcontractors)
                ->groupBy('subcontractor_id');

        foreach ($grouped as $subcontractorId => $rows) {

            /*
            |--------------------------------------------------------------------------
            | 作業内容を重複除去
            |--------------------------------------------------------------------------
            */

            $workTypeIds =
                $rows
                    ->pluck('work_type_id')
                    ->unique()
                    ->values();

            /*
            |--------------------------------------------------------------------------
            | 同じ会社に違う作業内容がある場合
            |--------------------------------------------------------------------------
            */

            if ($workTypeIds->count() <= 1) {
                continue;
            }

            $subcontractor =
                Subcontractor::find(
                    $subcontractorId
                );

            /*
            |--------------------------------------------------------------------------
            | 作業内容の組み合わせを確認
            |--------------------------------------------------------------------------
            */

            $workTypeNames = $workTypeIds
                ->map(function ($workTypeId) {

                    return WorkType::find(
                        $workTypeId
                    )?->name ?? '作業内容不明';
                })
                ->values();

            /*
            |--------------------------------------------------------------------------
            | 確認メッセージ
            |--------------------------------------------------------------------------
            */

            $warnings[] = [

                'type' => 'form',

                'subcontractor_id' =>
                    $subcontractorId,

                'subcontractor_name' =>
                    $subcontractor?->name
                    ?? '下請会社',

                'existing_work_type' =>
                    $workTypeNames->implode('、'),

                'new_work_type' =>
                    '複数の作業内容',

            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 重複除去
        |--------------------------------------------------------------------------
        */

        $warnings = collect($warnings)
            ->unique(function ($warning) {

                return implode('|', [
                    $warning['subcontractor_id'] ?? '',
                    $warning['existing_work_type'] ?? '',
                    $warning['new_work_type'] ?? '',
                    $warning['type'] ?? '',
                ]);
            })
            ->values()
            ->all();

        return $warnings;
    }

    /**
     * 下請確認メッセージ追加
     */
    private function addSubcontractorWarning(
        array &$warnings,
        $subcontractor,
        $existingWorkType,
        $newWorkType
    ) {

        $warnings[] = [

            'type' => 'database',

            'subcontractor_id' =>
                $subcontractor?->id,

            'subcontractor_name' =>
                $subcontractor?->name
                ?? '下請会社',

            'existing_work_type' =>
                $existingWorkType?->name
                ?? '作業内容不明',

            'new_work_type' =>
                $newWorkType?->name
                ?? '作業内容不明',
        ];
    }

    /**
     * 明細作成
     */
    private function createDetail(
        Request $request,
        DailyReport $dailyReport,
        $client,
        $employeeId,
        $subcontractorId,
        $index
    ) {

        /*
        |--------------------------------------------------------------------------
        | 勤務区分
        |--------------------------------------------------------------------------
        */

        $attendance = $this->findAttendance(
            $request,
            $index
        );

        /*
        |--------------------------------------------------------------------------
        | 作業内容
        |--------------------------------------------------------------------------
        */

        $workType = WorkType::find(
            $request->work_type_id[$index]
        );

        /*
        |--------------------------------------------------------------------------
        | 単価
        |--------------------------------------------------------------------------
        */

        $unitPrice = $this->getUnitPrice(
            $client,
            $workType
        );

        /*
        |--------------------------------------------------------------------------
        | 人工
        |--------------------------------------------------------------------------
        */

        $manHours =
            $request->man_hours[$index] ?? 0;

        /*
        |--------------------------------------------------------------------------
        | 残業
        |--------------------------------------------------------------------------
        */

        $overtimeHours =
            $request->overtime_hours[$index] ?? 0;

        /*
        |--------------------------------------------------------------------------
        | 売上
        |--------------------------------------------------------------------------
        */

        $sales = $this->calculateSales(
            $unitPrice,
            $manHours,
            $overtimeHours
        );

        /*
        |--------------------------------------------------------------------------
        | 保存
        |--------------------------------------------------------------------------
        */

        DailyReportDetail::create([

            'daily_report_id' =>
                $dailyReport->id,

            'employee_id' =>
                $employeeId,

            'subcontractor_id' =>
                $subcontractorId,

            'work_type_id' =>
                $request->work_type_id[$index],

            'man_hours' =>
                $manHours,

            'overtime_hours' =>
                $overtimeHours,

            'transportation_cost' =>
                $request->transportation_cost[$index] ?? 0,

            'expressway_cost' =>
                $request->expressway_cost[$index] ?? 0,

            'parking_cost' =>
                $request->parking_cost[$index] ?? 0,

            'sales' =>
                $sales,

            'note' =>
                $request->detail_note[$index] ?? null,

            'attendance_time_name' =>
                $attendance?->name,

            'start_time' =>
                $attendance?->start_time,

            'end_time' =>
                $attendance?->end_time,
        ]);
    }

    /**
     * 現場費保存
     */
    private function saveItems(
        Request $request,
        DailyReport $dailyReport
    ) {

        foreach (
            $request->item_name ?? []
            as $index => $name
        ) {

            if (empty($name)) {
                continue;
            }

            $quantity =
                $request->item_quantity[$index] ?? 1;

            DailyReportItem::create([

                'daily_report_id' =>
                    $dailyReport->id,

                'category' =>
                    $request->item_category[$index]
                    ?? '貸出',

                'name' =>
                    $name,

                'quantity' =>
                    $quantity,

                'unit' =>
                    $request->item_unit[$index]
                    ?? null,

                'unit_price' => 0,

                'amount' => 0,

                'note' =>
                    $request->item_note[$index]
                    ?? null,
            ]);
        }
    }

    /**
     * 売上計算
     */
    private function calculateSales(
        float $unitPrice,
        float $manHours,
        float $overtimeHours
    ): float {

        $sales =
            $unitPrice * $manHours;

        $sales +=
            ($unitPrice / 8)
            * 1.25
            * $overtimeHours;

        return $sales;
    }

    /**
     * 勤務区分取得
     */
    private function findAttendance(
        Request $request,
        int $index
    ): ?AttendanceTime {

        if (
            empty(
                $request->attendance_time_id[$index]
                ?? null
            )
        ) {
            return null;
        }

        return AttendanceTime::find(
            $request->attendance_time_id[$index]
        );
    }

    /**
     * 作業内容別単価
     */
    private function getUnitPrice(
        $client,
        WorkType $workType
    ): int {

        return match ($workType->name) {

            '解体工' =>
                $client->demolition_unit_price,

            '重機' =>
                $client->heavy_equipment_unit_price,

            '重機2' =>
                $client->heavy_equipment2_unit_price,

            'はつり' =>
                $client->chipping_unit_price,

            '石綿' =>
                $client->asbestos_unit_price,

            'トラック' =>
                $client->truck_unit_price,

            'ユニック' =>
                $client->unic_unit_price,

            default => 0,
        };
    }

    /**
     * 日報削除
     */
    public function destroy(
        DailyReport $dailyReport
    ) {

        DB::transaction(
            function () use ($dailyReport) {

                /*
                |--------------------------------------------------------------------------
                | 作業者明細削除
                |--------------------------------------------------------------------------
                */

                DailyReportDetail::where(
                    'daily_report_id',
                    $dailyReport->id
                )->delete();

                /*
                |--------------------------------------------------------------------------
                | 現場費削除
                |--------------------------------------------------------------------------
                */

                DailyReportItem::where(
                    'daily_report_id',
                    $dailyReport->id
                )->delete();

                /*
                |--------------------------------------------------------------------------
                | 日報本体削除
                |--------------------------------------------------------------------------
                */

                $dailyReport->delete();
            }
        );

        return redirect()
            ->route('daily-reports.index')
            ->with(
                'success',
                '日報を削除しました。'
            );
    }

    /**
     * 指定日付の在籍社員取得API
     *
     * create.blade.php の
     * 日付変更時に使用
     */
    public function employeesByDate(
        Request $request
    ) {

        $request->validate([
            'work_date' => 'required|date',
        ]);

        $workDate = Carbon::parse(
            $request->work_date
        );

        $employees =
            $this->getEmployedEmployees(
                $workDate
            );

        return response()->json(
            $employees->map(function ($employee) {

                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                ];
            })->values()
        );
    }
}