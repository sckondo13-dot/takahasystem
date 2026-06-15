<?php

namespace App\Http\Controllers;

use App\Models\Allowance;
use App\Models\AllowanceHistory;
use Illuminate\Http\Request;

class AllowanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allowances = Allowance::orderBy('type')
            ->orderBy('name')
            ->get();

        return view(
            'allowances.index',
            compact('allowances')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('allowances.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'type' => 'required',
            'amount' => 'required|integer',
        ]);

        $allowance = Allowance::create([

            'name' => $request->name,

            'type' => $request->type,

            'amount' => $request->amount,

            'note' => $request->note,
        ]);

        AllowanceHistory::create([

            'allowance_id' => $allowance->id,

            'amount' => $request->amount,

            'start_date' => today(),

            'end_date' => null,
        ]);

        return redirect()
            ->route('allowances.index')
            ->with('success', '手当を登録しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Allowance $allowance)
    {
        return view(
            'allowances.edit',
            compact('allowance')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        Allowance $allowance
    ) {
        $request->validate([
            'name' => 'required|max:255',
            'type' => 'required',
            'amount' => 'required|integer',
        ]);

        /*
    |--------------------------------------------------------------------------
    | 金額変更判定
    |--------------------------------------------------------------------------
    */

        if ($allowance->amount != $request->amount) {

            AllowanceHistory::where(
                'allowance_id',
                $allowance->id
            )
                ->whereNull('end_date')
                ->update([
                    'end_date' => today()->subDay(),
                ]);

            AllowanceHistory::create([

                'allowance_id' => $allowance->id,

                'amount' => $request->amount,

                'start_date' => today(),

                'end_date' => null,
            ]);
        }

        $allowance->update([

            'name' => $request->name,

            'type' => $request->type,

            'amount' => $request->amount,

            'note' => $request->note,
        ]);

        return redirect()
            ->route('allowances.index')
            ->with('success', '更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
