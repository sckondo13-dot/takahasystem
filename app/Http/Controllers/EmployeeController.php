<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Allowance;
use App\Models\EmployeeAllowance;

class EmployeeController extends Controller
{
    /**
     * 一覧
     */
    public function index()
    {
        $employees = Employee::orderBy('id')->get();

        return view('employees.index', compact('employees'));
    }

    /**
     * 新規画面
     */
    public function create()
    {
        $allowances = Allowance::orderBy('type')
            ->orderBy('name')
            ->get();

        return view(
            'employees.create',
            compact('allowances')
        );
    }

    /**
     * 登録
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        $employee = Employee::create([
            'name' => $request->name,
        ]);

        foreach ($request->allowances ?? [] as $allowanceId) {

            $allowance = Allowance::find($allowanceId);

            EmployeeAllowance::create([

                'employee_id' => $employee->id,

                'allowance_id' => $allowance->id,

                'allowance_name' => $allowance->name,

                'amount' => $allowance->amount,

                'start_date' => today(),

                'end_date' => null,

            ]);
        }

        return redirect()
            ->route('employees.index')
            ->with('success', '従業員を登録しました');
    }

    /**
     * 編集画面
     */
    public function edit(Employee $employee)
    {
        $fixedAllowances = Allowance::where('type', 'fixed')->get();

        $workAllowances = Allowance::where('type', 'work')->get();

        $allowances = Allowance::orderBy('type')
            ->orderBy('name')
            ->get();

        $currentAllowances = $employee
            ->employeeAllowances()
            ->whereNull('end_date')
            ->pluck('allowance_id')
            ->toArray();

        return view(
            'employees.edit',
            compact(
                'employee',
                'allowances',
                'currentAllowances'
            )
        );
    }

    /**
     * 更新
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        $employee->update([
            'name' => $request->name,
        ]);

        $today = today();

        /*
    |--------------------------------------------------------------------------
    | 現在有効な手当
    |--------------------------------------------------------------------------
    */
        $currentIds = $employee
            ->employeeAllowances()
            ->whereNull('end_date')
            ->pluck('allowance_id')
            ->toArray();

        /*
    |--------------------------------------------------------------------------
    | チェックされた手当
    |--------------------------------------------------------------------------
    */
        $newIds = $request->allowances ?? [];

        /*
    |--------------------------------------------------------------------------
    | チェックが外れた手当
    |--------------------------------------------------------------------------
    */
        $removedIds = array_diff(
            $currentIds,
            $newIds
        );

        if (!empty($removedIds)) {

            EmployeeAllowance::where(
                'employee_id',
                $employee->id
            )
                ->whereIn(
                    'allowance_id',
                    $removedIds
                )
                ->whereNull('end_date')
                ->update([
                    'end_date' => $today,
                ]);
        }

        /*
    |--------------------------------------------------------------------------
    | 新しく追加された手当
    |--------------------------------------------------------------------------
    */
        $addedIds = array_diff(
            $newIds,
            $currentIds
        );

        foreach ($addedIds as $allowanceId) {

            $allowance = Allowance::find($allowanceId);

            EmployeeAllowance::create([

                'employee_id' => $employee->id,

                'allowance_id' => $allowance->id,

                'allowance_name' => $allowance->name,

                'amount' => $allowance->amount,

                'start_date' => $today,

                'end_date' => null,

            ]);
        }

        return redirect()
            ->route('employees.index')
            ->with('success', '従業員を更新しました');
    }

    /**
     * 削除
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', '従業員を削除しました');
    }
}
