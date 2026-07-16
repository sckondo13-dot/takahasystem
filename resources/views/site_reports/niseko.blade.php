<x-app-layout>

    <div class="max-w-7xl mx-auto py-8">

        <div class="flex justify-between items-center mb-6">

            <h1 class="text-2xl font-bold">
                ニセコ木造解体 月報
            </h1>

            @if($showTable)
            <div class="flex gap-2">

                <a
                    href="{{ route('site-reports.niseko.pdf',['month'=>$month->format('Y-m')]) }}"
                    target="_blank"
                    class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-2 rounded shadow">

                    👁 PDF表示

                </a>

                <a
                    href="{{ route('site-reports.niseko.download',['month'=>$month->format('Y-m')]) }}"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded shadow">

                    📥 PDFダウンロード

                </a>

            </div>
            @endif

        </div>

        <form method="GET" class="mb-5">

            <input
                type="month"
                name="month"
                value="{{ $month->format('Y-m') }}"
                class="border rounded p-2">

            <button
                class="bg-blue-600 text-white px-4 py-2 rounded">

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

                            {{ preg_replace('/^木造解体：/u', '', $site->name) }}

                        </th>

                        @endforeach

                        <th class="border p-2">

                            人工合計

                        </th>

                        <th class="border p-2">

                            解体単価

                        </th>

                        <th class="border p-2">

                            売上

                        </th>

                        <th class="border p-2">

                            交通費

                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($rows as $row)

                    @php
                    $day=$row['date']->dayOfWeek;
                    @endphp

                    <tr>

                        <td class="text-center @if($day==0) text-red-600 @endif @if($day==6) text-blue-600 @endif">

                            {{ $row['date']->format('n/j') }}
                            （{{ ['日','月','火','水','木','金','土'][$day] }}）

                        </td>

                        @foreach($visibleSites as $site)

                        <td class="border p-2 text-center">

                            @if(isset($row['sites'][$site->id]))

                            {{ number_format($row['sites'][$site->id]['man']) }}

                            @endif

                        </td>

                        @endforeach

                        <td class="border p-2 text-center">

                            {{ number_format($row['total_man']) }}

                        </td>

                        <td class="border p-2 text-right">

                            @if($row['total_man']>0)

                            {{ number_format($client->demolition_unit_price) }}

                            @endif

                        </td>

                        <td class="border p-2 text-right">

                            @if($row['sales']>0)

                            {{ number_format($row['sales']) }}

                            @endif

                        </td>

                        <td class="border p-2 text-right">

                            @if($row['transportation']>0)

                            {{ number_format($row['transportation']) }}

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

                            {{ number_format($siteTotals[$site->id]) }}

                        </td>

                        @endforeach

                        <td class="border p-2 text-center">

                            {{ number_format($totalManHours) }}

                        </td>

                        <td class="border p-2 text-right">

                            {{ number_format($client->demolition_unit_price) }}

                        </td>

                        <td class="border p-2 text-right">

                            {{ number_format($totalSales) }}

                        </td>

                        <td class="border p-2 text-right">

                            {{ number_format($totalTransportation) }}

                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>

        @endif

    </div>

</x-app-layout>