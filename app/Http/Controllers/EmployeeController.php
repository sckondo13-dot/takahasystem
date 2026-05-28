<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

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
        return view('employees.create');
    }

    /**
     * 登録
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        Employee::create([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', '従業員を登録しました');
    }

    /**
     * 編集画面
     */
    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
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
