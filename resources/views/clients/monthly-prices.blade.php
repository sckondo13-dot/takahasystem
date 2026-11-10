<x-app-layout>
    <div class="max-w-5xl mx-auto py-10 px-4">

        <h1 class="text-2xl font-bold mb-2">
            月別単価設定
        </h1>

        <p class="text-gray-600 mb-6">
            元請：{{ $client->name }}
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
            action="{{ route('clients.monthly-prices', $client) }}"
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

        {{-- 単価保存 --}}
        <form
            method="POST"
            action="{{ route('clients.monthly-prices.update', $client) }}">
            @csrf

            <input
                type="hidden"
                name="month"
                value="{{ $month }}">

            <div class="bg-white rounded shadow overflow-hidden">

                <div class="p-5 border-b">
                    <h2 class="font-bold text-lg">
                        {{ \Carbon\Carbon::parse($month)->format('Y年n月') }} の基本単価
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        この月に適用する元請の基本単価を入力してください。
                    </p>
                </div>

                <div class="p-5 space-y-5">

                    @forelse ($workTypes as $workType)

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">

                        <label
                            for="price_{{ $workType->id }}"
                            class="font-semibold">
                            {{ $workType->name }}
                        </label>

                        <div class="md:col-span-2">
                            <div class="flex items-center gap-2">

                                <input
                                    type="number"
                                    id="price_{{ $workType->id }}"
                                    name="prices[{{ $workType->id }}]"
                                    min="0"
                                    step="1"
                                    value="{{ old(        'prices.' . $workType->id,        $prices->has($workType->id)            ? $prices[$workType->id]->unit_price            : ($basePrices[$workType->id] ?? 0)    ) }}"
                                    class="border rounded px-3 py-2 w-full"
                                    required>

                                <span>円</span>
                            </div>
                        </div>

                    </div>

                    @empty

                    <p class="text-red-600">
                        作業種別が登録されていません。
                    </p>

                    @endforelse

                </div>

                <div class="p-5 bg-gray-50 flex justify-between items-center">

                    <a
                        href="{{ route('clients.index') }}"
                        class="text-gray-600 underline">
                        元請一覧へ戻る
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
</x-app-layout>