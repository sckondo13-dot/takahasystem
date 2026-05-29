<x-app-layout>

    <div class="max-w-7xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-5">
            日報編集
        </h1>

        <form action="{{ route('daily-reports.update', $dailyReport) }}"
            method="POST">

            @csrf
            @method('PUT')

            {{-- 上部情報 --}}
            <div class="bg-white border rounded p-5 mb-5">

                <div class="grid grid-cols-2 gap-5">

                    {{-- 日付 --}}
                    <div>

                        <label class="block mb-1 font-bold">
                            日付
                        </label>

                        <input type="date"
                            name="work_date"
                            class="w-full border rounded p-2"
                            value="{{ old('work_date', $dailyReport->work_date->format('Y-m-d')) }}">

                    </div>

                    {{-- 現場 --}}
                    <div>

                        <label class="block mb-1 font-bold">
                            現場
                        </label>

                        <select name="site_id"
                            class="w-full border rounded p-2">

                            <option value="">
                                選択してください
                            </option>

                            @foreach($sites as $site)

                            <option value="{{ $site->id }}"
                                {{ old('site_id', $dailyReport->site_id) == $site->id ? 'selected' : '' }}>

                                {{ $site->name }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </div>

            {{-- 明細 --}}
            <div class="bg-white border rounded p-5">

                <div class="flex justify-between items-center mb-5">

                    <h2 class="text-xl font-bold">
                        作業者一覧
                    </h2>

                    <button type="button"
                        id="addRow"
                        class="bg-green-600 text-white px-4 py-2 rounded">

                        ＋ 行追加

                    </button>

                </div>

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
                                </th>

                            </tr>

                        </thead>

                        <tbody id="tableBody">

                            @foreach($dailyReport->details as $detail)

                            <tr>

                                {{-- 作業者 --}}
                                <td class="border p-2">

                                    <select name="worker[]"
                                        class="w-full border rounded p-2">

                                        <option value="">
                                            選択
                                        </option>

                                        @foreach($workers as $worker)

                                        <option value="{{ $worker['type'] }}_{{ $worker['id'] }}"

                                            @if($detail->employee_id)

                                            {{ $worker['type'] == 'employee' && $worker['id'] == $detail->employee_id ? 'selected' : '' }}

                                            @else

                                            {{ $worker['type'] == 'subcontractor' && $worker['id'] == $detail->subcontractor_id ? 'selected' : '' }}

                                            @endif
                                            >

                                            {{ $worker['name'] }}

                                        </option>

                                        @endforeach

                                    </select>

                                </td>

                                {{-- 作業内容 --}}
                                <td class="border p-2">

                                    <select name="work_type_id[]"
                                        class="w-full border rounded p-2">

                                        @foreach($workTypes as $workType)

                                        <option value="{{ $workType->id }}"
                                            {{ $detail->work_type_id == $workType->id ? 'selected' : '' }}>

                                            {{ $workType->name }}

                                        </option>

                                        @endforeach

                                    </select>

                                </td>

                                {{-- 人工 --}}
                                <td class="border p-2">

                                    <input type="number"
                                        step="0.25"
                                        name="man_hours[]"
                                        class="w-24 border rounded p-2"
                                        value="{{ $detail->man_hours }}">

                                </td>

                                {{-- 残業 --}}
                                <td class="border p-2">

                                    <input type="number"
                                        step="0.5"
                                        name="overtime_hours[]"
                                        class="w-24 border rounded p-2"
                                        value="{{ $detail->overtime_hours }}">

                                </td>

                                {{-- 交通費 --}}
                                <td class="border p-2">

                                    <input type="number"
                                        name="transportation_cost[]"
                                        class="w-24 border rounded p-2"
                                        value="{{ $detail->transportation_cost }}">

                                </td>

                                {{-- 高速 --}}
                                <td class="border p-2">

                                    <input type="number"
                                        name="expressway_cost[]"
                                        class="w-24 border rounded p-2"
                                        value="{{ $detail->expressway_cost }}">

                                </td>

                                {{-- 駐車場 --}}
                                <td class="border p-2">

                                    <input type="number"
                                        name="parking_cost[]"
                                        class="w-24 border rounded p-2"
                                        value="{{ $detail->parking_cost }}">

                                </td>

                                {{-- 削除 --}}
                                <td class="border p-2 text-center">

                                    <button type="button"
                                        class="removeRow bg-red-500 text-white px-3 py-1 rounded">

                                        削除

                                    </button>

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="mt-5 flex gap-3">

                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded shadow">

                    更新

                </button>

                <a href="{{ route('daily-reports.show', $dailyReport) }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded shadow">

                    戻る

                </a>

            </div>

        </form>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const addRowButton = document.getElementById('addRow');

            const tableBody = document.getElementById('tableBody');

            // 行追加
            addRowButton.addEventListener('click', () => {

                const firstRow = tableBody.querySelector('tr');

                const newRow = firstRow.cloneNode(true);

                // input初期化
                newRow.querySelectorAll('input').forEach(input => {

                    if (input.name.includes('man_hours')) {

                        input.value = 1;

                    } else {

                        input.value = 0;
                    }
                });

                // select初期化
                newRow.querySelectorAll('select').forEach(select => {

                    select.selectedIndex = 0;
                });

                tableBody.appendChild(newRow);
            });

            // 行削除
            document.addEventListener('click', (e) => {

                if (e.target.classList.contains('removeRow')) {

                    const rows = tableBody.querySelectorAll('tr');

                    if (rows.length > 1) {

                        e.target.closest('tr').remove();
                    }
                }
            });

        });
    </script>

</x-app-layout>