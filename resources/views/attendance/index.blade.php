<x-app-layout>

    <div class="max-w-7xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-5">
            個人出勤簿
        </h1>

        {{-- 検索 --}}
        <div class="bg-white border rounded p-5 mb-5">

            <form method="GET"
                class="flex flex-wrap gap-3 items-end">

                {{-- 社員 --}}
                <div>

                    <label class="block mb-1">
                        社員
                    </label>

                    <select name="employee_id"
                        class="border rounded p-2 w-60">

                        <option value="">
                            選択してください
                        </option>

                        @foreach($employees as $employee)

                        <option value="{{ $employee->id }}"
                            {{ $employeeId == $employee->id ? 'selected' : '' }}>

                            {{ $employee->name }}

                        </option>

                        @endforeach

                    </select>

                </div>

                {{-- 月 --}}
                <div>

                    <label class="block mb-1">
                        月
                    </label>

                    <input type="month"
                        name="month"
                        value="{{ $month->format('Y-m') }}"
                        class="border rounded p-2">

                </div>

                <div>

                    <button class="bg-blue-600 text-white px-5 py-2 rounded">

                        表示

                    </button>

                </div>

            </form>

        </div>

        {{-- 一覧 --}}
        @if($employeeId)

        <div class="bg-white border rounded p-5">

            <table class="w-full border">

                <thead class="bg-gray-100">

                    <tr>

                        <th class="border p-2">
                            日付
                        </th>

                        <th class="border p-2">
                            現場
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

                    </tr>

                </thead>

                <tbody>

                    @foreach($details as $detail)

                    <tr>

                        <td class="border p-2">

                            {{ $detail->dailyReport->work_date->format('Y-m-d') }}

                        </td>

                        <td class="border p-2">

                            {{ $detail->dailyReport->site->name }}

                        </td>

                        <td class="border p-2">

                            {{ $detail->workType->name }}

                        </td>

                        <td class="border p-2 text-right">

                            {{ $detail->man_hours }}

                        </td>

                        <td class="border p-2 text-right">

                            {{ $detail->overtime_hours }}

                        </td>

                    </tr>

                    @endforeach

                </tbody>

                {{-- 合計 --}}
                <tfoot class="bg-gray-100 font-bold">

                    <tr>

                        <td colspan="3"
                            class="border p-2 text-right">

                            合計

                        </td>

                        <td class="border p-2 text-right">

                            {{ $totalManHours }}

                        </td>

                        <td class="border p-2 text-right">

                            {{ $totalOvertime }}

                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>

        @endif

    </div>

</x-app-layout>