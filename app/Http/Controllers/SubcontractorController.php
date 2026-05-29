<?php

namespace App\Http\Controllers;

use App\Models\Subcontractor;
use Illuminate\Http\Request;

class SubcontractorController extends Controller
{
    /**
     * 一覧
     */
    public function index()
    {
        $subcontractors = Subcontractor::orderBy('id')->get();

        return view('subcontractors.index', compact('subcontractors'));
    }

    /**
     * 新規画面
     */
    public function create()
    {
        return view('subcontractors.create');
    }

    /**
     * 登録
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        Subcontractor::create([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('subcontractors.index')
            ->with('success', '下請けを登録しました');
    }

    /**
     * 編集画面
     */
    public function edit(Subcontractor $subcontractor)
    {
        return view('subcontractors.edit', compact('subcontractor'));
    }

    /**
     * 更新
     */
    public function update(Request $request, Subcontractor $subcontractor)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        $subcontractor->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('subcontractors.index')
            ->with('success', '下請けを更新しました');
    }

    /**
     * 削除
     */
    public function destroy(Subcontractor $subcontractor)
    {
        $subcontractor->delete();

        return redirect()
            ->route('subcontractors.index')
            ->with('success', '下請けを削除しました');
    }
}
