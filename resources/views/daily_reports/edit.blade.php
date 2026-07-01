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
                                    勤務区分
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
                                    備考
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
                                <td class="border p-2">

                                    <select
                                        name="attendance_time_id[]"
                                        class="w-full border rounded p-2">

                                        <option value="">
                                            選択してください
                                        </option>

                                        @foreach($attendanceTimes as $attendanceTime)

                                        <option
                                            value="{{ $attendanceTime->id }}"
                                            {{ $detail->attendance_time_name == $attendanceTime->name ? 'selected' : '' }}>

                                            {{ $attendanceTime->name }}

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
                                        step="0.5"
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
                                {{-- 備考 --}}
                                <td class="border p-2">

                                    <input type="text"
                                        name="detail_note[]"
                                        class="w-48 border rounded p-2"
                                        value="{{ $detail->detail_note }}">

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
            {{-- 現場費 --}}
            <div class="bg-white border rounded p-5 mt-5">

                <div class="flex justify-between items-center mb-5">

                    <h2 class="text-xl font-bold">
                        現場費
                    </h2>

                    <button
                        type="button"
                        id="addFreeItem"
                        class="bg-green-600 text-white px-4 py-2 rounded">

                        ＋ 項目追加

                    </button>

                </div>

                <table class="w-full border" id="freeItemTable">

                    <thead class="bg-gray-100">

                        <tr>

                            <th class="border p-2">
                                項目名
                            </th>

                            <th class="border p-2">
                                カテゴリ
                            </th>

                            <th class="border p-2">
                                数量
                            </th>

                            <th class="border p-2">
                                単位
                            </th>

                            <th class="border p-2">
                                備考
                            </th>

                            <th class="border p-2 w-20">

                            </th>

                        </tr>

                    </thead>

                    <tbody id="itemTableBody">

                        @forelse($dailyReport->items as $item)

                        <tr>

                            <td class="border p-2">

                                <input
                                    type="text"
                                    name="item_name[]"
                                    value="{{ $item->name }}"
                                    class="w-full border rounded p-2">

                            </td>

                            <td class="border p-2">

                                <select name="item_category[]" class="w-full border rounded p-2">

                                    <option value="貸出"
                                        {{ $item->category=='貸出'?'selected':'' }}>
                                        貸出
                                    </option>

                                    <option value="資材"
                                        {{ $item->category=='資材'?'selected':'' }}>
                                        資材
                                    </option>

                                    <option value="その他"
                                        {{ $item->category=='その他'?'selected':'' }}>
                                        その他
                                    </option>

                                </select>

                            </td>



                            <td class="border p-2">

                                <input
                                    type="number"
                                    step="0.01"
                                    name="item_quantity[]"
                                    value="{{ $item->quantity }}" class="w-full border rounded p-2">

                            </td>

                            <td class="border p-2">

                                <input
                                    type="text"
                                    name="item_unit[]"
                                    value="{{ $item->unit }}" class="w-full border rounded p-2">

                            </td>

                            <td class="border p-2 text-center">

                                <input
                                    type="text"
                                    name="item_note[]"
                                    value="{{ $item->note }}"
                                    class="w-full border rounded p-2">

                            </td>

                            <td class="border p-2 text-center">

                                <button
                                    type="button"
                                    class="removeItemRow bg-red-500 text-white px-3 py-1 rounded">

                                    削除

                                </button>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td class="border p-2">

                                <input type="text"
                                    name="item_name[]" class="w-full border rounded p-2">

                            </td>

                            <td class="border p-2">

                                <select name="item_category[]" class="w-full border rounded p-2">

                                    <option value="貸出">
                                        貸出
                                    </option>

                                    <option value="資材">
                                        資材
                                    </option>

                                    <option value="その他">
                                        その他
                                    </option>

                                </select>

                            </td>



                            <td class="border p-2">

                                <input type="number"
                                    step="0.01"
                                    name="item_quantity[]"
                                    value="1" class="w-full border rounded p-2">

                            </td>

                            <td class="border p-2">

                                <input type="text"
                                    name="item_unit[]" class="w-full border rounded p-2">

                            </td>

                            <td class="border p-2">

                                <input type="text"
                                    name="item_note[]" class="w-full border rounded p-2">

                            </td>

                            <td class="border p-2 text-center">

                                <button
                                    type="button"
                                    class="removeItemRow bg-red-500 text-white px-3 py-1 rounded">

                                    削除

                                </button>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>
            <div class="mt-5">

                <label class="block mb-1 font-bold">
                    全体備考
                </label>

                <textarea
                    name="note"
                    rows="3"
                    class="w-full border rounded p-2">{{ old('note', $dailyReport->note) }}</textarea>

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

                    if (input.name === 'man_hours[]') {

                        input.value = 1;

                    } else if (input.name === 'detail_note[]') {

                        input.value = '';

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