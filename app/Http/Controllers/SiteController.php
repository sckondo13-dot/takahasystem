<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /**
     * 一覧
     */
    public function index(Request $request)
    {
        $query = Site::with('client');

        // 現場名検索
        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        // 元請絞り込み
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // 状況絞り込み
        if ($request->filled('status')) {

            $today = now()->startOfMonth();

            switch ($request->status) {

                case 'active':

                    $query->whereDate('contract_start', '<=', $today)
                        ->where(function ($q) use ($today) {
                            $q->whereNull('contract_end')
                                ->orWhereDate('contract_end', '>=', $today);
                        });

                    break;

                case 'future':

                    $query->whereDate('contract_start', '>', $today);

                    break;

                case 'finished':

                    $query->whereNotNull('contract_end')
                        ->whereDate('contract_end', '<', $today);

                    break;
            }
        }

        $sites = $query
            ->orderBy('contract_start', 'desc')
            ->orderBy('name')
            ->get();

        $clients = Client::orderBy('name')->get();

        return view('sites.index', compact(
            'sites',
            'clients'
        ));
    }

    /**
     * 新規画面
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();

        return view('sites.create', compact('clients'));
    }

    /**
     * 登録
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'contract_type' => 'required|in:請負,常用',
            'contract_start' => 'nullable|date',
            'contract_end' => 'nullable|date|after_or_equal:contract_start',

            'contract_amount' => 'nullable|numeric|min:0',
            'remaining_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        Site::create([
            'client_id' => $request->client_id,
            'name' => $request->name,
            'contract_type' => $request->contract_type,
            'contract_start' => $request->contract_start,
            'contract_end' => $request->contract_end,
            'contract_amount' => $request->contract_amount ?? 0,
            'remaining_rate' => $request->remaining_rate ?? 100,
        ]);

        return redirect()
            ->route('sites.index')
            ->with('success', '現場を登録しました');
    }

    /**
     * 編集画面
     */
    public function edit(Site $site)
    {
        $clients = Client::orderBy('name')->get();

        return view('sites.edit', compact(
            'site',
            'clients'
        ));
    }

    /**
     * 更新
     */
    public function update(Request $request, Site $site)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'contract_type' => 'required|in:請負,常用',
            'contract_start' => 'nullable|date',
            'contract_end' => 'nullable|date|after_or_equal:contract_start',

            'contract_amount' => 'nullable|numeric|min:0',
            'remaining_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $site->update([
            'client_id' => $request->client_id,
            'name' => $request->name,
            'contract_type' => $request->contract_type,
            'contract_start' => $request->contract_start,
            'contract_end' => $request->contract_end,
            'contract_amount' => $request->contract_amount ?? 0,
            'remaining_rate' => $request->remaining_rate ?? 100,
        ]);

        return redirect()
            ->route('sites.index')
            ->with('success', '現場を更新しました');
    }

    /**
     * 削除
     */
    public function destroy(Site $site)
    {
        $site->delete();

        return redirect()
            ->route('sites.index')
            ->with('success', '現場を削除しました');
    }
}
