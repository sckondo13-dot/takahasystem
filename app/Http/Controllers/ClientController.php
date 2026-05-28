<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * 一覧
     */
    public function index()
    {
        $clients = Client::orderBy('name')->get();

        return view('clients.index', compact('clients'));
    }

    /**
     * 新規画面
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * 登録
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        Client::create($request->all());

        return redirect()
            ->route('clients.index')
            ->with('success', '元請けを登録しました');
    }

    /**
     * 編集画面
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * 更新
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        $client->update($request->all());

        return redirect()
            ->route('clients.index')
            ->with('success', '元請けを更新しました');
    }

    /**
     * 削除
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', '元請けを削除しました');
    }
}
