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
    public function index()
    {
        $sites = Site::with('client')
            ->orderBy('name')
            ->get();

        return view('sites.index', compact('sites'));
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
            'client_id' => 'required',
            'name' => 'required|max:255',
            'contract_type' => 'required',
        ]);

        Site::create([
            'client_id' => $request->client_id,
            'name' => $request->name,
            'contract_type' => $request->contract_type,
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
            'client_id' => 'required',
            'name' => 'required|max:255',
            'contract_type' => 'required',
        ]);

        $site->update([
            'client_id' => $request->client_id,
            'name' => $request->name,
            'contract_type' => $request->contract_type,
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
