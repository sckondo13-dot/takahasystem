<x-app-layout>
    <div class="max-w-5xl mx-auto py-10 px-4">

        <h1 class="text-2xl font-bold mb-6">
            月別売上一括再計算
        </h1>

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

        {{-- 説明 --}}
        <div class="bg-white p-5 rounded shadow mb-6">
            <p class="text-gray-700 mb-2">
                指定した年月の既存日報について、現在設定されている単価を使って売上を一括再計算します。
            </p>

            <p class="text-sm text-gray-500">
                単価の優先順位：現場別月別単価 → 元請別月別単価 → 元請の基本単価
            </p>
        </div>

        {{-- 対象年月選択 --}}
        <form
            method="GET"
            action="{{ route('monthly-sales-recalculation.index') }}"
            class="bg-white p-5 rounded shadow mb-6">

            <label class="block font-bold mb-2">
                対象年月
            </label>

            <div class="flex flex-wrap gap-3 items-center">
                <input
                    type="month"
                    name="month"
                    value="{{ $month }}"
                    class="border rounded px-3 py-2"
                    required>

                <button
                    type="submit"
                    class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-2 rounded">
                    表示
                </button>
            </div>
        </form>

        {{-- 再計算対象 --}}
        <div class="bg-white rounded shadow overflow-hidden">

            <div class="p-5 border-b">
                <h2 class="font-bold text-lg">
                    {{ \Carbon\Carbon::parse($month)->format('Y年n月') }} の集計
                </h2>
            </div>

            {{-- 全体集計 --}}
            <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4">

                <div class="bg-blue-50 rounded p-4">
                    <p class="text-sm text-gray-600">日報件数</p>
                    <p class="text-2xl font-bold text-blue-700">
                        {{ number_format($reportCount) }} 件
                    </p>
                </div>

                <div class="bg-green-50 rounded p-4">
                    <p class="text-sm text-gray-600">現場数</p>
                    <p class="text-2xl font-bold text-green-700">
                        {{ number_format($siteCount) }} 現場
                    </p>
                </div>

                <div class="bg-purple-50 rounded p-4">
                    <p class="text-sm text-gray-600">日報明細件数</p>
                    <p class="text-2xl font-bold text-purple-700">
                        {{ number_format($detailCount) }} 件
                    </p>
                </div>

            </div>
            {{-- 売上比較 --}}
            <div class="px-5 pb-5">

                <h3 class="font-bold text-lg mb-3">
                    売上再計算のシミュレーション
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- 再計算前 --}}
                    <div class="bg-gray-100 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2">
                            再計算前の売上合計
                        </p>

                        <p class="text-2xl font-bold text-gray-800">
                            {{ number_format($beforeSales) }} 円
                        </p>
                    </div>

                    {{-- 再計算後 --}}
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm text-blue-700 mb-2">
                            再計算後の売上合計（予定）
                        </p>

                        <p class="text-2xl font-bold text-blue-700">
                            {{ number_format($afterSales) }} 円
                        </p>
                    </div>

                    {{-- 差額 --}}
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <p class="text-sm text-yellow-700 mb-2">
                            売上の差額
                        </p>

                        <p class="text-2xl font-bold
                {{ $salesDifference > 0 ? 'text-green-700' : ($salesDifference < 0 ? 'text-red-700' : 'text-gray-800') }}">

                            @if ($salesDifference > 0)
                            +{{ number_format($salesDifference) }} 円
                            @elseif ($salesDifference < 0)
                                {{ number_format($salesDifference) }} 円
                                @else
                                0 円
                                @endif

                                </p>
                    </div>

                </div>

                <p class="text-sm text-gray-500 mt-3">
                    ※ 再計算後の金額は現在の単価設定を使用した試算です。
                    この画面を表示しただけでは日報の売上は変更されません。
                </p>

            </div>

            {{-- 現場ごとの集計 --}}
            <div class="px-5 pb-5">

                <h3 class="font-bold mb-3">
                    現場別の内訳
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">

                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="text-left p-3">現場名</th>
                                <th class="text-right p-3">日報件数</th>
                                <th class="text-right p-3">明細件数</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($siteSummaries as $summary)
                            <tr class="border-b">
                                <td class="p-3">
                                    {{ $summary['site_name'] }}
                                </td>

                                <td class="p-3 text-right">
                                    {{ number_format($summary['report_count']) }} 件
                                </td>

                                <td class="p-3 text-right">
                                    {{ number_format($summary['detail_count']) }} 件
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-5 text-center text-gray-500">
                                    対象の日報はありません。
                                </td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

            </div>

            <div class="border-t p-5">

                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded mb-5">
                    <p class="font-bold mb-1">
                        再計算時の注意
                    </p>

                    <ul class="text-sm list-disc pl-5 space-y-1">
                        <li>指定年月の日報明細の売上が再計算されます。</li>
                        <li>人工・残業時間は現在登録されている値を使用します。</li>
                        <li>交通費・高速代・駐車場代などは変更されません。</li>
                        <li>現在の単価設定が過去の日報にも適用されます。</li>
                    </ul>
                </div>

                <form
                    method="POST"
                    action="{{ route('monthly-sales-recalculation.store') }}"
                    onsubmit="return confirm('{{ $month }} の日報売上を一括再計算します。よろしいですか？');">

                    @csrf

                    <input type="hidden" name="month" value="{{ $month }}">

                    <div class="flex flex-wrap justify-between items-center gap-3">

                        <a
                            href="{{ route('daily-reports.index') }}"
                            class="text-gray-600 underline">
                            日報一覧へ戻る
                        </a>

                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded disabled:opacity-50"
                            @disabled($detailCount===0)>
                            {{ $month }} の売上を一括再計算
                        </button>

                    </div>
                </form>

            </div>

        </div>

    </div>
</x-app-layout>