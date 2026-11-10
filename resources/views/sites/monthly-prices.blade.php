<x-app-layout>
    <div class="max-w-5xl mx-auto py-10 px-4">

        <h1 class="text-2xl font-bold mb-2">
            現場別・月別単価設定
        </h1>

        <p class="text-gray-600 mb-6">
            元請：{{ $site->client->name }}<br>
            現場：{{ $site->name }}
        </p>

        @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-5">
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-5">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- 対象月選択 --}}
        <form
            method="GET"
            action="{{ route('sites.monthly-prices', $site) }}"
            class="bg-white p-5 rounded shadow mb-6">
            <label class="block font-bold mb-2">
                対象年月
            </label>

            <div class="flex gap-3 items-center">
                <input
                    type="month"
                    name="month"
                    value="{{ $month }}"
                    class="border rounded px-3 py-2"
                    required>

                <button
                    type="submit"
                    class="bg-gray-700 text-white px-5 py-2 rounded">
                    表示
                </button>
            </div>
        </form>

        {{-- 単価設定 --}}
        <form
            method="POST"
            action="{{ route('sites.monthly-prices.update', $site) }}">
            @csrf

            <input
                type="hidden"
                name="month"
                value="{{ $month }}">

            <div class="bg-white rounded shadow overflow-hidden">

                <div class="p-5 border-b">
                    <h2 class="font-bold text-lg">
                        {{ \Carbon\Carbon::parse($month)->format('Y年n月') }} の現場別単価
                    </h2>

                    <p class="text-sm text-gray-500 mt-2">
                        個別単価を設定する作業種別にチェックを入れてください。
                        チェックを外すと元請の単価を使用します。
                    </p>
                </div>

                <div class="p-5 space-y-6">

                    @foreach ($workTypes as $workType)

                    @php
                    $hasSitePrice = $sitePrices->has($workType->id);

                    $displayPrice = $hasSitePrice
                    ? $sitePrices[$workType->id]->unit_price
                    : (
                    $clientPrices->has($workType->id)
                    ? $clientPrices[$workType->id]->unit_price
                    : ($basePrices[$workType->id] ?? 0)
                    );
                    @endphp

                    <div class="border-b pb-5">

                        <div class="flex flex-wrap items-center justify-between gap-3">

                            <label class="font-bold text-lg">
                                {{ $workType->name }}
                            </label>

                            <label class="flex items-center gap-2 text-sm">
                                <input
                                    type="checkbox"
                                    name="use_site_price[{{ $workType->id }}]"
                                    value="1"
                                    class="site-price-checkbox"
                                    data-id="{{ $workType->id }}"
                                    @checked(
                                    old( 'use_site_price.' . $workType->id,
                                session()->hasOldInput('use_site_price')
                                ? false
                                : $hasSitePrice
                                )
                                )
                                >

                                現場個別単価を設定する
                            </label>

                        </div>

                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">

                            <div>
                                <label class="block text-sm text-gray-600 mb-1">
                                    単価
                                </label>

                                <div class="flex items-center gap-2">

                                    <input
                                        type="number"
                                        name="prices[{{ $workType->id }}]"
                                        id="price_{{ $workType->id }}"
                                        min="0"
                                        step="1"
                                        value="{{ old(
                                                'prices.' . $workType->id,
                                                $hasSitePrice
                                                    ? $sitePrices[$workType->id]->unit_price
                                                    : ''
                                            ) }}"
                                        class="border rounded px-3 py-2 w-full site-price-input"
                                        data-id="{{ $workType->id }}"
                                        @disabled(
                                        !old( 'use_site_price.' . $workType->id,
                                    session()->hasOldInput('use_site_price')
                                    ? false
                                    : $hasSitePrice
                                    )
                                    )
                                    >

                                    <span>円</span>

                                </div>
                            </div>

                            <div class="text-sm text-gray-600 md:pt-7">

                                @if ($clientPrices->has($workType->id))
                                元請の{{ $workType->name }}単価：
                                {{ number_format($clientPrices[$workType->id]->unit_price) }}円
                                @else
                                元請の{{ $workType->name }}単価：
                                {{ number_format($basePrices[$workType->id] ?? 0) }}円
                                @endif

                            </div>

                        </div>

                    </div>

                    @endforeach

                </div>

                <div class="p-5 bg-gray-50 flex flex-wrap justify-between items-center gap-3">

                    <a
                        href="{{ route('sites.index') }}"
                        class="text-gray-600 underline">
                        現場一覧へ戻る
                    </a>

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded">
                        この月の単価を保存
                    </button>

                </div>

            </div>
        </form>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.site-price-checkbox').forEach(function(checkbox) {

                checkbox.addEventListener('change', function() {

                    const id = this.dataset.id;
                    const input = document.getElementById('price_' + id);

                    input.disabled = !this.checked;

                    if (!this.checked) {
                        input.value = '';
                    }

                });

            });

        });
    </script>
</x-app-layout>