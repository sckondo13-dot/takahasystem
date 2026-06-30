<x-app-layout>

    <div class="max-w-7xl mx-auto py-10">

        {{-- タイトル --}}
        <div class="flex justify-between items-center mb-5">

            <h1 class="text-2xl font-bold">
                日報詳細
            </h1>

            <div class="flex gap-2">

                <a href="{{ route('daily-reports.edit', $dailyReport) }}"
                    class="bg-yellow-500 text-white px-5 py-2 rounded">

                    編集

                </a>

                <a href="{{ route('daily-reports.index') }}"
                    class="bg-gray-600 text-white px-5 py-2 rounded">

                    ← 一覧へ戻る

                </a>

            </div>

        </div>

        {{-- 基本情報 --}}
        <div class="bg-white border rounded p-5 mb-5">

            <div class="grid grid-cols-2 gap-5">

                <div>

                    <div class="text-sm text-gray-500">
                        日付
                    </div>

                    <div class="text-xl font-bold">

                        {{ $dailyReport->work_date->format('Y-m-d') }}
                    </div>

                </div>

                <div>

                    <div class="text-sm text-gray-500">
                        現場
                    </div>

                    <div class="text-xl font-bold">

                        {{ $dailyReport->site->name }}

                    </div>

                </div>

            </div>

        </div>

        {{-- 明細 --}}
        <div class="bg-white border rounded p-5">

            <h2 class="text-xl font-bold mb-5">
                作業者一覧
            </h2>

            <div class="overflow-x-auto">

                <table class="w-full border">

                    <thead class="bg-gray-100">

                        <tr>

                            <th class="border p-2">
                                作業者
                            </th>

                            <th class="border p-2">
                                作業内容
                            </th>

                            <th class="border p-2">
                                出退勤時間
                            </th>

                            <th class="border p-2">
                                人工
                            </th>

                            <th class="border p-2">
                                残業
                            </th>

                            <th class="border p-2">
                                交通費
                            </th>

                            <th class="border p-2">
                                高速
                            </th>

                            <th class="border p-2">
                                駐車場
                            </th>

                            <th class="border p-2">
                                売上
                            </th>
                            <th class="border p-2">
                                備考
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($dailyReport->details as $detail)

                        <tr>

                            {{-- 作業者 --}}
                            <td class="border p-2">

                                @if($detail->employee)

                                【社員】
                                {{ $detail->employee->name }}

                                @endif

                                @if($detail->subcontractor)

                                【下請】
                                {{ $detail->subcontractor->name }}

                                @endif

                            </td>

                            {{-- 作業内容 --}}
                            <td class="border p-2">

                                {{ $detail->workType->name }}

                            </td>

                            <td class="border p-2 text-center">

                                @if($detail->attendance_time_name)

                                <div class="font-semibold">
                                    {{ $detail->attendance_time_name }}
                                </div>

                                <div class="text-sm text-gray-500">

                                    {{ substr($detail->start_time, 0, 5) }}
                                    ～
                                    {{ substr($detail->end_time, 0, 5) }}

                                </div>

                                @else

                                -

                                @endif

                            </td>

                            {{-- 人工 --}}
                            <td class="border p-2 text-right">

                                {{ $detail->man_hours }}

                            </td>

                            {{-- 残業 --}}
                            <td class="border p-2 text-right">

                                {{ $detail->overtime_hours }}

                            </td>

                            {{-- 交通費 --}}
                            <td class="border p-2 text-right">

                                {{ number_format($detail->transportation_cost) }}

                            </td>

                            {{-- 高速 --}}
                            <td class="border p-2 text-right">

                                {{ number_format($detail->expressway_cost) }}

                            </td>

                            {{-- 駐車場 --}}
                            <td class="border p-2 text-right">

                                {{ number_format($detail->parking_cost) }}

                            </td>

                            <td class="border p-2 text-right">

                                {{ number_format($detail->sales) }}

                            </td>
                            <td class="border p-2">
                                {{ $detail->note }}
                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>
                @if($dailyReport->note)

                <div class="bg-yellow-50 border rounded p-4 mb-5">

                    <div class="font-bold mb-2">
                        全体備考
                    </div>

                    {!! nl2br(e($dailyReport->note)) !!}

                </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>