<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Site;
use Illuminate\Http\Request;
use App\Models\WorkType;
use App\Models\SiteMonthlyUnitPrice;
use App\Models\ClientMonthlyUnitPrice;
use Illuminate\Support\Facades\DB;

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

    /**
     * 現場の月別単価設定画面
     */
    public function monthlyPrices(Request $request, Site $site)
    {
        $request->validate([
            'month' => 'nullable|date_format:Y-m',
        ]);

        $month = $request->input('month', now()->format('Y-m'));
        $priceMonth = $month . '-01';

        $site->load('client');

        $workTypes = WorkType::whereIn('name', [
            '解体工',
            '重機',
            '重機2',
            'はつり',
            '石綿',
            'トラック',
            'ユニック',
        ])->orderBy('id')->get();

        // 現場に設定されている個別単価
        $sitePrices = SiteMonthlyUnitPrice::where('site_id', $site->id)
            ->whereDate('price_month', $priceMonth)
            ->get()
            ->keyBy('work_type_id');

        // 元請に設定されている月別単価
        $clientPrices = ClientMonthlyUnitPrice::where('client_id', $site->client_id)
            ->whereDate('price_month', $priceMonth)
            ->get()
            ->keyBy('work_type_id');

        // 既存の元請基本単価
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
                ? ($site->client->{$column} ?? 0)
                : 0;
        }

        return view('sites.monthly-prices', compact(
            'site',
            'month',
            'workTypes',
            'sitePrices',
            'clientPrices',
            'basePrices'
        ));
    }


    /**
     * 現場の月別単価保存
     */
    public function updateMonthlyPrices(Request $request, Site $site)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'use_site_price' => 'nullable|array',
            'use_site_price.*' => 'nullable|in:1',
            'prices' => 'nullable|array',
            'prices.*' => 'nullable|integer|min:0',
        ]);

        $priceMonth = $validated['month'] . '-01';

        $workTypes = WorkType::whereIn('name', [
            '解体工',
            '重機',
            '重機2',
            'はつり',
            '石綿',
            'トラック',
            'ユニック',
        ])->get();

        DB::transaction(function () use (
            $validated,
            $site,
            $priceMonth,
            $workTypes
        ) {
            foreach ($workTypes as $workType) {

                $useSitePrice = isset(
                    $validated['use_site_price'][$workType->id]
                );

                if ($useSitePrice) {

                    $unitPrice = $validated['prices'][$workType->id] ?? null;

                    if ($unitPrice === null || $unitPrice === '') {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'prices.' . $workType->id =>
                            $workType->name . 'の個別単価を入力してください。',
                        ]);
                    }

                    SiteMonthlyUnitPrice::updateOrCreate(
                        [
                            'site_id' => $site->id,
                            'work_type_id' => $workType->id,
                            'price_month' => $priceMonth,
                        ],
                        [
                            'unit_price' => $unitPrice,
                        ]
                    );
                } else {

                    // 元請単価を使用する場合、現場個別単価を削除
                    SiteMonthlyUnitPrice::where('site_id', $site->id)
                        ->where('work_type_id', $workType->id)
                        ->whereDate('price_month', $priceMonth)
                        ->delete();
                }
            }
        });

        return redirect()
            ->route('sites.monthly-prices', [
                'site' => $site->id,
                'month' => $validated['month'],
            ])
            ->with('success', '現場別の月別単価を保存しました。');
    }
}
