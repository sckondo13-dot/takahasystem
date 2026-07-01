<x-app-layout>

    <div class="max-w-7xl mx-auto py-8">

        <h1 class="text-2xl font-bold mb-6">
            現場別月報
        </h1>

        <form class="flex flex-wrap gap-3 mb-6">

            <select
                name="site_id"
                class="border rounded p-2">

                <option value="">
                    現場選択
                </option>

                @foreach($sites as $s)

                <option
                    value="{{ $s->id }}"
                    {{ $siteId==$s->id?'selected':'' }}>

                    {{ $s->name }}

                </option>

                @endforeach

            </select>

            <input
                type="month"
                name="month"
                value="{{ $month->format('Y-m') }}"
                class="border rounded p-2">

            <button
                class="bg-blue-600 text-white px-5 rounded">

                表示

            </button>

        </form>

        @if($site)

        <div class="bg-white rounded shadow p-5 mb-5">

            <div class="grid md:grid-cols-3 gap-5">

                <div>

                    <div class="text-gray-500">
                        現場
                    </div>

                    <div class="font-bold text-lg">
                        {{ $site->name }}
                    </div>

                </div>

                <div>

                    <div class="text-gray-500">
                        元請
                    </div>

                    <div class="font-bold">

                        {{ $site->client->name }}

                    </div>

                </div>

                <div>

                    <div class="text-gray-500">
                        対象月
                    </div>

                    <div class="font-bold">

                        {{ $month->format('Y年m月') }}

                    </div>

                </div>

            </div>

        </div>

        <div class="overflow-auto max-h-[70vh] border rounded">

            <table class="min-w-full border text-sm">

                <thead class="sticky top-0 z-20 bg-gray-100 shadow">

                    <tr>

                        <th class="border p-2 bg-gray-100 whitespace-nowrap">日付</th>

                        <th class="border p-2 bg-gray-100 whitespace-nowrap">解体</th>

                        <th class="border p-2 bg-gray-100 whitespace-nowrap">重機</th>

                        <th class="border p-2 bg-gray-100 whitespace-nowrap">重機2</th>

                        <th class="border p-2 bg-gray-100 whitespace-nowrap">ガス</th>

                        <th class="border p-2 bg-gray-100 whitespace-nowrap">はつり</th>

                        <th class="border p-2 bg-gray-100 whitespace-nowrap">石綿</th>

                        <th class="border p-2 bg-gray-100 whitespace-nowrap">トラック</th>

                        <th class="border p-2 bg-gray-100 whitespace-nowrap">人工</th>

                        <th class="border p-2 bg-gray-100 whitespace-nowrap">売上</th>

                        <th class="border p-2 bg-gray-100 whitespace-nowrap">残業</th>

                        <th class="border p-2 bg-gray-100 whitespace-nowrap">交通費</th>

                        <th class="border p-2 bg-gray-100 whitespace-nowrap">高速代</th>

                        <th class="border p-2 bg-gray-100 whitespace-nowrap">駐車場代</th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($dates as $date)

                    @php
                    $data = $reportMap[$date->format('Y-m-d')] ?? null;
                    @endphp

                    <tr
                        class="@if($date->dayOfWeek === 0) bg-red-50 @elseif($date->dayOfWeek === 6) bg-blue-50 @endif">

                        <td class="border p-2 text-center font-bold @if($date->dayOfWeek === 0)bg-red-100 text-red-700 @elseif($date->dayOfWeek === 6) bg-blue-100 text-blue-700 @endif">

                            {{ $date->format('n/j') }}

                            <br>

                            <span class="text-xs">
                                （{{ ['日','月','火','水','木','金','土'][$date->dayOfWeek] }}）
                            </span>

                        </td>

                        <td class="border text-center">{{ $data['解体工'] ?? '' }}</td>

                        <td class="border text-center">{{ $data['重機'] ?? '' }}</td>

                        <td class="border text-center">{{ $data['重機２'] ?? '' }}</td>

                        <td class="border text-center">{{ $data['ガス工'] ?? '' }}</td>

                        <td class="border text-center">{{ $data['はつり'] ?? '' }}</td>

                        <td class="border text-center">{{ $data['石綿'] ?? '' }}</td>

                        <td class="border text-center">{{ $data['トラック'] ?? '' }}</td>

                        <td class="border text-center">

                            {{ $data['total_man'] ?? '' }}

                        </td>

                        <td class="border text-right">

                            @if($data)

                            {{ number_format($data['sales']) }}

                            @endif

                        </td>


                        <td class="border text-center">

                            {{ $data['overtime'] ?? '' }}

                        </td>

                        <td class="border text-right">

                            @if($data)

                            {{ number_format($data['transportation']) }}

                            @endif

                        </td>

                        <td class="border text-right">

                            @if($data)

                            {{ number_format($data['expressway']) }}

                            @endif

                        </td>

                        <td class="border text-right">

                            @if($data)

                            {{ number_format($data['parking']) }}

                            @endif

                        </td>

                    </tr>

                    @if($data && $data['items']->count())

                    <tr>

                        <td colspan="14"
                            class="bg-gray-50 p-3">

                            <div class="font-bold mb-1">

                                貸出機材

                            </div>

                            @foreach($data['items'] as $item)

                            <div>

                                {{ $item->name }}

                                ×

                                {{ $item->quantity }}

                                {{ $item->unit }}

                            </div>

                            @endforeach

                        </td>

                    </tr>

                    @endif

                    @endforeach

                </tbody>

                <tfoot class="bg-gray-100 font-bold">

                    <tr>

                        <td class="border text-right">

                            合計

                        </td>

                        <td colspan="7"></td>

                        <td class="border text-right">

                            {{ $totalManHours }}

                        </td>

                        <td class="border text-right">

                            {{ number_format($totalSales) }}

                        </td>

                        <td class="border text-right">

                            {{ $totalOvertime }}

                        </td>

                        <td class="border text-right">

                            {{ number_format($totalTransportation) }}

                        </td>

                        <td class="border text-right">

                            {{ number_format($totalExpressway) }}

                        </td>

                        <td class="border text-right">

                            {{ number_format($totalParking) }}

                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-5 mt-6">

            {{-- 合計人工 --}}
            <div class="bg-white rounded shadow p-4">
                <div class="text-gray-500">
                    合計人工
                </div>
                <div class="text-2xl font-bold text-blue-600">
                    {{ number_format($totalManHours, 1) }}
                </div>
            </div>

            {{-- 合計売上 --}}
            <div class="bg-white rounded shadow p-4">
                <div class="text-gray-500">
                    合計売上
                </div>
                <div class="text-2xl font-bold text-green-600">
                    ¥{{ number_format($totalSales) }}
                </div>
            </div>

            {{-- 合計残業 --}}
            <div class="bg-white rounded shadow p-4">
                <div class="text-gray-500">
                    合計残業
                </div>
                <div class="text-2xl font-bold text-orange-600">
                    {{ number_format($totalOvertime, 1) }}h
                </div>
            </div>

            {{-- 交通費 --}}
            <div class="bg-white rounded shadow p-4">
                <div class="text-gray-500">
                    交通費
                </div>
                <div class="text-2xl font-bold">
                    ¥{{ number_format($totalTransportation) }}
                </div>
            </div>

            {{-- 高速代 --}}
            <div class="bg-white rounded shadow p-4">
                <div class="text-gray-500">
                    高速代
                </div>
                <div class="text-2xl font-bold">
                    ¥{{ number_format($totalExpressway) }}
                </div>
            </div>

            {{-- 駐車場代 --}}
            <div class="bg-white rounded shadow p-4">
                <div class="text-gray-500">
                    駐車場代
                </div>
                <div class="text-2xl font-bold">
                    ¥{{ number_format($totalParking) }}
                </div>
            </div>

        </div>

        @endif

    </div>

</x-app-layout>