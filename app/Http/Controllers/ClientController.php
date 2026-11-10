<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\WorkType;
use App\Models\ClientMonthlyUnitPrice;
use Illuminate\Support\Facades\DB;

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
            'address' => 'nullable|string|max:1000',
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
            'address' => 'nullable|string|max:1000',
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

    /**
     * 元請の月別単価編集画面
     */
    public function monthlyPrices(Request $request, Client $client)
    {
        $month = $request->input('month', now()->format('Y-m'));

        $request->validate([
            'month' => 'nullable|date_format:Y-m',
        ]);

        $priceMonth = $month . '-01';

        $workTypes = WorkType::whereIn('name', [
            '解体工',
            '重機',
            '重機2',
            '重機２',
            'はつり',
            '石綿',
            'トラック',
            'ユニック',
        ])->orderBy('id')->get();

        $basePriceColumns = [
            '解体工' => 'demolition_unit_price',
            '重機' => 'heavy_equipment_unit_price',
            '重機2' => 'heavy_equipment2_unit_price',
            'はつり' => 'chipping_unit_price',
            '石綿' => 'asbestos_unit_price',
            'トラック' => 'truck_unit_price',
            'ユニック' => 'unic_unit_price',
        ];

        $basePrices = [];

        foreach ($workTypes as $workType) {
            $column = $basePriceColumns[$workType->name] ?? null;

            $basePrices[$workType->id] = $column
                ? ($client->{$column} ?? 0)
                : 0;
        }

        $prices = ClientMonthlyUnitPrice::where('client_id', $client->id)
            ->whereDate('price_month', $priceMonth)
            ->get()
            ->keyBy('work_type_id');

        return view('clients.monthly-prices', compact(
            'client',
            'month',
            'workTypes',
            'prices',
            'basePrices'
        ));
    }

    /**
     * 元請の月別単価保存
     */
    public function updateMonthlyPrices(Request $request, Client $client)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'prices' => 'required|array',
            'prices.*' => 'required|integer|min:0',
        ]);

        $priceMonth = $validated['month'] . '-01';

        DB::transaction(function () use ($validated, $client, $priceMonth) {
            foreach ($validated['prices'] as $workTypeId => $unitPrice) {

                $workType = WorkType::findOrFail($workTypeId);

                ClientMonthlyUnitPrice::updateOrCreate(
                    [
                        'client_id' => $client->id,
                        'work_type_id' => $workType->id,
                        'price_month' => $priceMonth,
                    ],
                    [
                        'unit_price' => $unitPrice,
                    ]
                );
            }
        });

        return redirect()
            ->route('clients.monthly-prices', [
                'client' => $client->id,
                'month' => $validated['month'],
            ])
            ->with('success', '月別単価を保存しました。');
    }
}
