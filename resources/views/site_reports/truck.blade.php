<x-app-layout>

    <div class="max-w-7xl mx-auto py-8">


        <div class="flex justify-between items-center mb-6">

            <h1 class="text-2xl font-bold">
                北海道リサイクラー トラック明細
            </h1>

            @if($showTable && $visibleSites->isNotEmpty())

            <div class="flex gap-2">

                {{-- PDF表示 --}}
                <a
                    href="{{ route('site-reports.truck.pdf', ['month' => $month->format('Y-m')]) }}"
                    target="_blank"
                    class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-2 rounded shadow">

                    👁 PDF表示

                </a>

                {{-- PDFダウンロード --}}
                <a
                    href="{{ route('site-reports.truck.download', ['month' => $month->format('Y-m')]) }}"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded shadow">

                    📥 PDFダウンロード

                </a>

            </div>

            @endif

        </div>


        {{-- 月選択 --}}
        <form method="GET" class="mb-5">

            <input
                type="month"
                name="month"
                value="{{ $month->format('Y-m') }}"
                class="border rounded p-2">

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

                表示

            </button>

        </form>

        @if($showTable)

        <div class="overflow-auto">

            <table class="min-w-full border border-gray-300 text-sm">

                <thead class="bg-gray-100">

                    <tr>

                        <th class="border p-2">
                            日付
                        </th>

                        @foreach($visibleSites as $site)

                        <th class="border p-2">

                            {{ $site->name }}

                            <div class="text-xs font-normal text-gray-600 mt-1">
                                単価：{{ number_format($site->truck_unit_price_for_month ?? 0) }}円
                            </div>

                        </th>

                        @endforeach

                        <th class="border p-2">
                            人工合計
                        </th>

                        <th class="border p-2">
                            残業
                        </th>

                        <th class="border p-2">
                            売上
                        </th>

                        <th class="border p-2">
                            高速代
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($rows as $row)

                    @php
                    $day = $row['date']->dayOfWeek;
                    @endphp

                    <tr>

                        <td class="border p-2 text-center
                            @if($day == 0) text-red-600 @endif
                            @if($day == 6) text-blue-600 @endif">

                            {{ $row['date']->format('n/j') }}

                            （{{ ['日','月','火','水','木','金','土'][$day] }}）

                        </td>

                        @foreach($visibleSites as $site)

                        <td class="border p-2 text-center">

                            @if(isset($row['sites'][$site->id]))

                            {{ number_format($row['sites'][$site->id]['man'], 1) }}

                            @endif

                        </td>

                        @endforeach

                        <td class="border p-2 text-center font-bold">

                            {{ number_format($row['total_man'], 1) }}

                        </td>

                        {{-- 残業 --}}
                        <td class="border p-2 text-center">

                            @if($row['overtime'] > 0)
                            {{ number_format($row['overtime'], 2) }}
                            @endif

                        </td>

                        <td class="border p-2 text-right">

                            @if($row['sales'] > 0)
                            {{ number_format($row['sales']) }}
                            @endif

                        </td>

                        {{-- 高速代 --}}
                        <td class="border p-2 text-right">

                            @if($row['expressway'] > 0)
                            {{ number_format($row['expressway']) }}
                            @endif

                        </td>
                    </tr>

                    @endforeach

                </tbody>

                <tfoot class="bg-gray-100 font-bold">

                    <tr>

                        <td class="border p-2">
                            合計
                        </td>

                        @foreach($visibleSites as $site)

                        <td class="border p-2 text-center">

                            {{ number_format($siteTotals[$site->id] ?? 0, 1) }}

                        </td>

                        @endforeach

                        <td class="border p-2 text-center">

                            {{ number_format($totalManHours, 1) }}

                        </td>

                        {{-- 残業合計 --}}
                        <td class="border p-2 text-center">

                            {{ number_format($totalOvertime, 2) }}

                        </td>

                        <td class="border p-2 text-right">

                            {{ number_format($totalSales) }}

                        </td>

                        {{-- 高速代合計 --}}
                        <td class="border p-2 text-right">

                            {{ number_format($totalExpressway) }}

                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>

        @if($visibleSites->isEmpty())

        <p class="text-gray-500 mt-5">
            選択した月にトラックの作業実績はありません。
        </p>

        @endif

        @endif

    </div>

</x-app-layout>