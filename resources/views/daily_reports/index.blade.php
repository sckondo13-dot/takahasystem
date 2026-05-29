<x-app-layout>

    <div class="max-w-full mx-auto py-10 px-5">

        {{-- header --}}
        <div class="flex justify-between items-center mb-5">

            <div class="flex items-center gap-3">

                {{-- 前月 --}}
                <a href="{{ route('daily-reports.index', ['month' => $prevMonth]) }}"
                    class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">

                    ←

                </a>

                {{-- 月 --}}
                <h1 class="text-2xl font-bold">

                    {{ $month->format('Y年n月') }}

                </h1>

                {{-- 次月 --}}
                <a href="{{ route('daily-reports.index', ['month' => $nextMonth]) }}"
                    class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">

                    →

                </a>

            </div>

            {{-- 新規 --}}
            <a href="{{ route('daily-reports.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">

                ＋ 新規登録

            </a>

        </div>

        {{-- 月選択 --}}
        <div class="mb-5">

            <form method="GET"
                class="flex items-center gap-3">

                <input type="month"
                    name="month"
                    value="{{ $month->format('Y-m') }}"
                    class="border rounded p-2">

                <button class="bg-gray-700 text-white px-4 py-2 rounded">

                    表示

                </button>

            </form>

        </div>

        {{-- success --}}
        @if(session('success'))

        <div class="bg-green-100 text-green-700 p-3 rounded mb-5">

            {{ session('success') }}

        </div>

        @endif

        {{-- table --}}
        <div class="overflow-x-auto">

            <table class="border-collapse border w-full">

                <thead>

                    <tr class="bg-gray-100">

                        <th class="border p-2 sticky left-0 bg-gray-100 z-10 min-w-[100px]">

                            日付

                        </th>

                        @foreach($sites as $site)

                        <th class="border p-2 whitespace-nowrap min-w-[120px]">

                            {{ $site->name }}

                        </th>

                        @endforeach

                    </tr>

                </thead>

                <tbody>

                    @foreach($dates as $date)

                    <tr class="@if($date->dayOfWeek === 0)bg-red-50
                    @elseif($date->dayOfWeek === 6)
                    bg-blue-50
                    @endif">

                        {{-- 日付 --}}
                        <td class="border p-2 bg-gray-50 font-bold sticky left-0 @if($date->dayOfWeek === 0)
                        text-red-600
                        @elseif($date->dayOfWeek === 6)
                        text-blue-600
                        @endif">

                            {{ $date->format('n/j') }}

                            ({{ ['日','月','火','水','木','金','土'][$date->dayOfWeek] }})

                        </td>

                        {{-- 現場 --}}
                        @foreach($sites as $site)

                        <td class="border p-2 text-center">

                            @php

                            $data = $reportMap[
                            $date->format('Y-m-d')
                            ][$site->id] ?? null;

                            @endphp

                            @if($data)

                            <a href="{{ route('daily-reports.show', $data['report']) }}"
                                class="inline-flex items-center justify-center
                                                  w-10 h-10 rounded-full
                                                  bg-blue-600 hover:bg-blue-700
                                                  text-white font-bold">

                                {{ $data['count'] }}

                            </a>

                            @endif

                        </td>

                        @endforeach

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>