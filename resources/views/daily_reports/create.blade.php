<x-app-layout>

    <div class="max-w-7xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-5">
            日報登録
        </h1>

        <form action="{{ route('daily-reports.store') }}"
            method="POST"
            id="dailyReportForm">

            @csrf

            {{-- =========================================================
                下請確認
            ========================================================== --}}
            @if(session('subcontractor_confirmations'))

            {{-- 確認済みであることをControllerへ伝える --}}
            <input type="hidden"
                name="confirm_subcontractor"
                value="1">

            <div class="bg-yellow-50 border-2 border-yellow-400 rounded p-5 mb-5">

                <h2 class="text-lg font-bold text-yellow-800 mb-3">
                    下請の登録内容を確認してください
                </h2>

                <p class="mb-4">
                    同じ日・同じ現場に、すでに登録されている下請会社、
                    または今回のフォーム内で別の作業内容が登録されています。
                </p>

                @foreach(session('subcontractor_confirmations') as $warning)

                <div class="bg-white border rounded p-3 mb-2">

                    <div class="font-bold">
                        【下請】{{ $warning['subcontractor_name'] }}
                    </div>

                    @if(!empty($warning['existing_work_type']))

                    <div>
                        既存：
                        <span class="font-bold">
                            {{ $warning['existing_work_type'] }}
                        </span>
                    </div>

                    @endif

                    @if(!empty($warning['new_work_type']))

                    <div>
                        今回：
                        <span class="font-bold text-blue-600">
                            {{ $warning['new_work_type'] }}
                        </span>
                    </div>

                    @endif

                    @if(!empty($warning['source']))

                    <div class="text-sm text-gray-500 mt-1">
                        {{ $warning['source'] }}
                    </div>

                    @endif

                </div>

                @endforeach

                <p class="font-bold text-yellow-800 mt-4 mb-3">
                    このまま登録しますか？
                </p>

                <div class="flex gap-3">

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">

                        このまま登録する

                    </button>

                    <a
                        href="{{ route('daily-reports.create') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded">

                        キャンセル

                    </a>

                </div>

            </div>

            @endif


            {{-- =========================================================
                上部情報
            ========================================================== --}}
            <div class="bg-white border rounded p-5 mb-5">

                <div class="grid grid-cols-2 gap-5">

                    {{-- 日付 --}}
                    <div>

                        <label class="block mb-1 font-bold">
                            日付
                        </label>

                        <input
                            type="date"
                            id="work_date"
                            name="work_date"
                            class="w-full border rounded p-2"
                            value="{{ old('work_date', $workDate->format('Y-m-d')) }}">

                    </div>


                    {{-- 現場 --}}
                    <div>

                        <label class="block mb-1 font-bold">
                            現場
                        </label>

                        <select
                            name="site_id"
                            id="site_id"
                            class="w-full border rounded p-2">

                            @foreach($sites as $site)

                            <option
                                value="{{ $site->id }}"
                                {{ old('site_id') == $site->id ? 'selected' : '' }}>

                                {{ $site->name }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </div>


            {{-- =========================================================
                作業者
            ========================================================== --}}
            <div class="bg-white border rounded p-5">

                <div class="flex justify-between items-center mb-5">

                    <h2 class="text-xl font-bold">
                        作業者一覧
                    </h2>

                    <button
                        type="button"
                        id="addRow"
                        class="bg-green-600 text-white px-4 py-2 rounded">

                        ＋ 行追加

                    </button>

                </div>


                <div class="overflow-x-auto">

                    <table
                        class="w-full border"
                        id="detailTable">

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

                                <th class="border p-2"></th>

                            </tr>

                        </thead>


                        @php
                        $oldWorkers = old('worker', ['']);
                        $oldAttendanceTimes = old('attendance_time_id', []);
                        $oldWorkTypes = old('work_type_id', []);
                        $oldManHours = old('man_hours', []);
                        $oldOvertimeHours = old('overtime_hours', []);
                        $oldTransportationCosts = old('transportation_cost', []);
                        $oldExpresswayCosts = old('expressway_cost', []);
                        $oldParkingCosts = old('parking_cost', []);
                        $oldDetailNotes = old('detail_note', []);
                        @endphp

                        <tbody id="tableBody">

                            @foreach($oldWorkers as $index => $oldWorker)

                            <tr>

                                {{-- 作業者 --}}
                                <td class="border p-2">

                                    <select
                                        name="worker[]"
                                        class="worker-select w-full border rounded p-2">

                                        <option value="">
                                            選択
                                        </option>

                                        @foreach($workers as $worker)

                                        <option
                                            value="{{ $worker['type'] }}_{{ $worker['id'] }}"
                                            {{ $oldWorker === $worker['type'] . '_' . $worker['id'] ? 'selected' : '' }}>

                                            {{ $worker['name'] }}

                                        </option>

                                        @endforeach

                                    </select>

                                </td>


                                {{-- 勤務区分 --}}
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
                                            {{ ($oldAttendanceTimes[$index] ?? '') == $attendanceTime->id ? 'selected' : '' }}>

                                            {{ $attendanceTime->name }}

                                        </option>

                                        @endforeach

                                    </select>

                                </td>


                                {{-- 作業内容 --}}
                                <td class="border p-2">

                                    <select
                                        name="work_type_id[]"
                                        class="w-full border rounded p-2">

                                        @foreach($workTypes as $workType)

                                        <option
                                            value="{{ $workType->id }}"
                                            {{ ($oldWorkTypes[$index] ?? '') == $workType->id ? 'selected' : '' }}>

                                            {{ $workType->name }}

                                        </option>

                                        @endforeach

                                    </select>

                                </td>


                                {{-- 人工 --}}
                                <td class="border p-2">

                                    <input
                                        type="number"
                                        step="0.5"
                                        name="man_hours[]"
                                        class="w-24 border rounded p-2"
                                        value="{{ $oldManHours[$index] ?? 1 }}">

                                </td>


                                {{-- 残業 --}}
                                <td class="border p-2">

                                    <input
                                        type="number"
                                        step="0.5"
                                        name="overtime_hours[]"
                                        class="w-24 border rounded p-2"
                                        value="{{ $oldOvertimeHours[$index] ?? 0 }}">

                                </td>


                                {{-- 交通費 --}}
                                <td class="border p-2">

                                    <input
                                        type="number"
                                        name="transportation_cost[]"
                                        class="w-24 border rounded p-2"
                                        value="{{ $oldTransportationCosts[$index] ?? 0 }}">

                                </td>


                                {{-- 高速 --}}
                                <td class="border p-2">

                                    <input
                                        type="number"
                                        name="expressway_cost[]"
                                        class="w-24 border rounded p-2"
                                        value="{{ $oldExpresswayCosts[$index] ?? 0 }}">

                                </td>


                                {{-- 駐車場 --}}
                                <td class="border p-2">

                                    <input
                                        type="number"
                                        name="parking_cost[]"
                                        class="w-24 border rounded p-2"
                                        value="{{ $oldParkingCosts[$index] ?? 0 }}">

                                </td>


                                {{-- 備考 --}}
                                <td class="border p-2">

                                    <input
                                        type="text"
                                        name="detail_note[]"
                                        class="w-48 border rounded p-2"
                                        value="{{ $oldDetailNotes[$index] ?? '' }}">

                                </td>


                                {{-- 削除 --}}
                                <td class="border p-2 text-center">

                                    <button
                                        type="button"
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


            {{-- =========================================================
                現場費
            ========================================================== --}}
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


                <table
                    class="w-full border"
                    id="freeItemTable">

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

                            <th class="border p-2 w-20"></th>

                        </tr>

                    </thead>


                    <tbody id="freeItemBody">

                        <tr>

                            <td class="border p-2">

                                <input
                                    type="text"
                                    name="item_name[]"
                                    class="w-full border rounded p-2"
                                    placeholder="例：鉄板5×10">

                            </td>


                            <td class="border p-2">

                                <select
                                    name="item_category[]"
                                    class="w-full border rounded p-2">

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

                                <input
                                    type="number"
                                    name="item_quantity[]"
                                    class="w-full border rounded p-2"
                                    value="1">

                            </td>


                            <td class="border p-2">

                                <input
                                    type="text"
                                    name="item_unit[]"
                                    class="w-full border rounded p-2"
                                    placeholder="枚・台・本">

                            </td>


                            <td class="border p-2">

                                <input
                                    type="text"
                                    name="item_note[]"
                                    class="w-full border rounded p-2">

                            </td>


                            <td class="border p-2 text-center">

                                <button
                                    type="button"
                                    class="removeFreeItem bg-red-500 text-white px-3 py-1 rounded">

                                    削除

                                </button>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            {{-- =========================================================
                全体備考
            ========================================================== --}}
            <div class="mt-5">

                <label class="block mb-1 font-bold">
                    全体備考
                </label>

                <textarea
                    name="note"
                    rows="3"
                    class="w-full border rounded p-2">{{ old('note') }}</textarea>

            </div>


            {{-- =========================================================
                登録
            ========================================================== --}}
            <div class="mt-5">

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded shadow">

                    登録

                </button>

            </div>

        </form>

    </div>


    {{-- =============================================================
        JavaScript
    ============================================================== --}}
    <script>
        const subcontractors = @json(
            collect($workers) -> where('type', 'subcontractor') -> values()
        );
        document.addEventListener('DOMContentLoaded', () => {

            const workDateInput =
                document.getElementById('work_date');

            const tableBody =
                document.getElementById('tableBody');

            const addRowButton =
                document.getElementById('addRow');



            /*
            |----------------------------------------------------------------------
            | 社員プルダウン更新
            |----------------------------------------------------------------------
            */

            async function updateEmployees() {

                const workDate =
                    workDateInput.value;

                if (!workDate) {
                    return;
                }

                try {

                    const response = await fetch(
                        `{{ route('daily-reports.employees-by-date') }}?work_date=${encodeURIComponent(workDate)}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );


                    if (!response.ok) {
                        throw new Error(
                            '社員情報の取得に失敗しました'
                        );
                    }


                    const employees =
                        await response.json();


                    /*
                    |------------------------------------------------------------------
                    | 現在の社員選択を保存
                    |------------------------------------------------------------------
                    */

                    const currentValues = [];

                    tableBody
                        .querySelectorAll('.worker-select')
                        .forEach(select => {

                            currentValues.push(
                                select.value
                            );

                        });


                    /*
                    |------------------------------------------------------------------
                    | 全行の社員プルダウンを更新
                    |------------------------------------------------------------------
                    */

                    tableBody
                        .querySelectorAll('.worker-select')
                        .forEach((select, index) => {

                            const currentValue =
                                currentValues[index];


                            /*
                            |----------------------------------------------------------
                            | プルダウンを初期化
                            |----------------------------------------------------------
                            */

                            select.innerHTML = '';


                            const emptyOption =
                                document.createElement('option');

                            emptyOption.value = '';

                            emptyOption.textContent =
                                '選択';

                            select.appendChild(
                                emptyOption
                            );


                            /*
                            |----------------------------------------------------------
                            | 社員
                            |----------------------------------------------------------
                            */

                            employees.forEach(employee => {

                                const option =
                                    document.createElement('option');

                                option.value =
                                    `employee_${employee.id}`;

                                option.textContent =
                                    `【社員】${employee.name}`;

                                select.appendChild(
                                    option
                                );

                            });


                            /*
                            |----------------------------------------------------------
                            | 下請
                            |----------------------------------------------------------
                            |
                            | 下請は日付に関係なく表示
                            */

                            subcontractors.forEach(worker => {

                                const option =
                                    document.createElement('option');

                                option.value =
                                    `subcontractor_${worker.id}`;

                                option.textContent =
                                    worker.name;

                                select.appendChild(
                                    option
                                );

                            });


                            /*
                            |----------------------------------------------------------
                            | 以前選択していた値を復元
                            |----------------------------------------------------------
                            */

                            if (
                                currentValue &&
                                Array.from(select.options)
                                .some(option =>
                                    option.value === currentValue
                                )
                            ) {

                                select.value =
                                    currentValue;

                            } else {

                                select.value = '';

                            }

                        });

                } catch (error) {

                    console.error(error);

                    alert(
                        '社員情報の取得に失敗しました。'
                    );

                }

            }


            /*
            |----------------------------------------------------------------------
            | 日付変更時
            |----------------------------------------------------------------------
            */

            workDateInput.addEventListener(
                'change',
                updateEmployees
            );


            /*
            |----------------------------------------------------------------------
            | 初期表示
            |----------------------------------------------------------------------
            */

            updateEmployees();



            /*
            |--------------------------------------------------------------------------
            | 行追加
            |--------------------------------------------------------------------------
            */

            addRowButton.addEventListener('click', () => {

                const firstRow =
                    tableBody.querySelector('tr');

                const newRow =
                    firstRow.cloneNode(true);


                /*
                |--------------------------------------------------------------
                | input初期化
                |--------------------------------------------------------------
                */

                newRow
                    .querySelectorAll('input')
                    .forEach(input => {

                        if (
                            input.name ===
                            'man_hours[]'
                        ) {

                            input.value = 1;

                        } else if (
                            input.name ===
                            'detail_note[]'
                        ) {

                            input.value = '';

                        } else {

                            input.value = 0;

                        }

                    });


                /*
                |--------------------------------------------------------------
                | select初期化
                |--------------------------------------------------------------
                */

                newRow
                    .querySelectorAll('select')
                    .forEach(select => {

                        select.selectedIndex = 0;

                    });


                tableBody.appendChild(newRow);

            });


            /*
            |--------------------------------------------------------------------------
            | 行削除
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', (e) => {

                if (
                    e.target.classList.contains(
                        'removeRow'
                    )
                ) {

                    const rows =
                        tableBody.querySelectorAll('tr');

                    if (rows.length > 1) {

                        e.target
                            .closest('tr')
                            .remove();

                    }

                }

            });


            /*
            |--------------------------------------------------------------------------
            | 現場費追加
            |--------------------------------------------------------------------------
            */

            const addFreeItem =
                document.getElementById('addFreeItem');

            const freeItemBody =
                document.getElementById('freeItemBody');


            addFreeItem.addEventListener('click', () => {

                const firstRow =
                    freeItemBody.querySelector('tr');

                const newRow =
                    firstRow.cloneNode(true);


                newRow
                    .querySelectorAll('input')
                    .forEach(input => {

                        if (
                            input.name ===
                            'item_quantity[]'
                        ) {

                            input.value = 1;

                        } else {

                            input.value = '';

                        }

                    });


                freeItemBody.appendChild(newRow);

            });


            /*
            |--------------------------------------------------------------------------
            | 現場費削除
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', (e) => {

                if (
                    e.target.classList.contains(
                        'removeFreeItem'
                    )
                ) {

                    const rows =
                        freeItemBody.querySelectorAll('tr');

                    if (rows.length > 1) {

                        e.target
                            .closest('tr')
                            .remove();

                    }

                }

            });

        });
    </script>

</x-app-layout>