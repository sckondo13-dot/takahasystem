<x-app-layout>

    <div class="max-w-7xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-5">
            個人出勤簿
        </h1>

        {{-- 検索 --}}
        <div class="bg-white border rounded p-5 mb-5">

            <div class="flex flex-wrap justify-between items-end gap-4">

                <form
                    method="GET"
                    class="flex flex-wrap gap-3 items-end">

                    {{-- 社員 --}}
                    <div>

                        <label class="block mb-1">
                            社員
                        </label>

                        <select
                            name="employee_id"
                            class="border rounded p-2 w-60">

                            <option value="">
                                選択してください
                            </option>

                            @foreach($employees as $employee)

                            <option
                                value="{{ $employee->id }}"
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

                        <input
                            type="month"
                            name="month"
                            value="{{ $month->format('Y-m') }}"
                            class="border rounded p-2">

                    </div>

                    <div>

                        <button
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">

                            表示

                        </button>

                    </div>

                </form>

                @if($employeeId)

                <a
                    href="{{ route('attendance.pdf',[
                'employee_id'=>$employeeId,
                'month'=>$month->format('Y-m')
            ]) }}"
                    target="_blank"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded shadow">

                    📄 PDF出力

                </a>

                @endif

            </div>

        </div>

        {{-- 一覧 --}}
        @if($employeeId)

        <div class="bg-white border rounded p-5">

            <table class="w-full border">

                <thead class="bg-gray-100">

                    <tr>

                        <th class="border p-2">
                            日付（曜日）
                        </th>

                        <th class="border p-2">
                            現場
                        </th>

                        <th class="border p-2">
                            作業内容
                        </th>
                        <th class="border p-2">
                            出勤
                        </th>

                        <th class="border p-2">
                            退勤
                        </th>

                        <th class="border p-2">
                            休憩
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
                            高速代
                        </th>

                        <th class="border p-2">
                            駐車場代
                        </th>

                        <th class="border p-2">
                            単価
                        </th>

                        <th class="border p-2">
                            作業手当
                        </th>

                        <th class="border p-2">
                            個人備考
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($details as $detail)

                    <tr>

                        {{-- 日付 --}}
                        <td class="border p-2 @if($detail->dailyReport->work_date->dayOfWeek === 0) text-red-600 @elseif($detail->dailyReport->work_date->dayOfWeek === 6) text-blue-600 @endif">

                            {{ $detail->dailyReport->work_date->format('Y/m/d') }}

                            （{{ ['日','月','火','水','木','金','土']
                [$detail->dailyReport->work_date->dayOfWeek] }}）

                        </td>

                        {{-- 現場 --}}
                        <td class="border p-2 @if($detail->dailyReport->work_date->dayOfWeek === 0) text-red-600 @elseif($detail->dailyReport->work_date->dayOfWeek === 6) text-blue-600 @endif">

                            {{ $detail->dailyReport->site->name }}

                        </td>

                        {{-- 作業内容 --}}
                        <td class="border p-2 @if($detail->dailyReport->work_date->dayOfWeek === 0) text-red-600 @elseif($detail->dailyReport->work_date->dayOfWeek === 6) text-blue-600 @endif">

                            {{ $detail->workType->name }}

                        </td>
                        {{-- 出勤 --}}
                        <td class="border p-2 text-center">

                            {{ $detail->start_time_formatted }}

                        </td>
                        {{-- 退勤 --}}
                        <td class="border p-2 text-center">

                            {{ $detail->end_time_formatted }}

                        </td>
                        {{-- 休憩 --}}
                        <td class="border p-2 text-center">

                            {{ $detail->break_hours_formatted }}h

                        </td>

                        {{-- 人工 --}}
                        <td class="border p-2 @if($detail->dailyReport->work_date->dayOfWeek === 0) text-red-600 @elseif($detail->dailyReport->work_date->dayOfWeek === 6) text-blue-600 @endif">

                            {{ $detail->man_hours }}

                        </td>

                        {{-- 残業 --}}
                        <td class="border p-2 @if($detail->dailyReport->work_date->dayOfWeek === 0) text-red-600 @elseif($detail->dailyReport->work_date->dayOfWeek === 6) text-blue-600 @endif">

                            {{ $detail->overtime_hours }}

                        </td>

                        {{-- 交通費 --}}
                        <td class="border p-2 @if($detail->dailyReport->work_date->dayOfWeek === 0) text-red-600 @elseif($detail->dailyReport->work_date->dayOfWeek === 6) text-blue-600 @endif">

                            {{ number_format($detail->transportation_cost) }}

                        </td>

                        {{-- 高速代 --}}
                        <td class="border p-2 @if($detail->dailyReport->work_date->dayOfWeek === 0) text-red-600 @elseif($detail->dailyReport->work_date->dayOfWeek === 6) text-blue-600 @endif">

                            {{ number_format($detail->expressway_cost) }}

                        </td>

                        {{-- 駐車場代 --}}
                        <td class="border p-2 @if($detail->dailyReport->work_date->dayOfWeek === 0) text-red-600 @elseif($detail->dailyReport->work_date->dayOfWeek === 6) text-blue-600 @endif">

                            {{ number_format($detail->parking_cost) }}

                        </td>

                        {{-- 単価 --}}
                        <td class="border p-2 @if($detail->dailyReport->work_date->dayOfWeek === 0) text-red-600 @elseif($detail->dailyReport->work_date->dayOfWeek === 6) text-blue-600 @endif">

                            {{ number_format($detail->unit_price) }}

                        </td>

                        {{-- 作業手当 --}}
                        <td class="border p-2 @if($detail->dailyReport->work_date->dayOfWeek === 0) text-red-600 @elseif($detail->dailyReport->work_date->dayOfWeek === 6) text-blue-600 @endif">

                            {{ number_format($detail->work_allowance) }}

                        </td>

                        {{-- 個人備考 --}}
                        <td class="border p-2 @if($detail->dailyReport->work_date->dayOfWeek === 0) text-red-600 @elseif($detail->dailyReport->work_date->dayOfWeek === 6) text-blue-600 @endif">

                            {{ $detail->note }}

                        </td>

                    </tr>

                    @endforeach

                </tbody>

                {{-- 合計 --}}
                <tfoot class="bg-gray-100 font-bold">

                    <tr>

                        <td colspan="6"
                            class="border p-2 text-right">

                            合計

                        </td>

                        <td class="border p-2 text-right">

                            {{ $totalManHours }}

                        </td>

                        <td class="border p-2 text-right">

                            {{ $totalOvertime }}

                        </td>

                        <td class="border p-2 text-right">

                            {{ number_format($totalTransportation) }}

                        </td>

                        <td class="border p-2 text-right">

                            {{ number_format($totalExpressway) }}

                        </td>

                        <td class="border p-2 text-right">

                            {{ number_format($totalParking) }}

                        </td>

                        <td></td>

                        <td class="border p-2 text-right">

                            {{ number_format($totalWorkAllowance) }}

                        </td>

                        <td></td>

                    </tr>

                </tfoot>

            </table>
            @if($fixedAllowances->count())

            <div class="bg-white border rounded p-5 mt-5">

                <h2 class="font-bold text-lg mb-3">

                    固定手当

                </h2>

                <table class="w-full border">

                    <thead class="bg-gray-100">

                        <tr>

                            <th class="border p-2">
                                手当名
                            </th>

                            <th class="border p-2">
                                金額
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($fixedAllowances as $allowance)

                        <tr>

                            <td class="border p-2">

                                {{ $allowance->allowance_name }}

                            </td>

                            <td class="border p-2 text-right">

                                {{ number_format($allowance->amount) }}

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                    <tfoot class="bg-gray-100 font-bold">

                        <tr>

                            <td class="border p-2 text-right">

                                合計

                            </td>

                            <td class="border p-2 text-right">

                                {{ number_format($fixedAllowanceTotal) }}

                            </td>

                        </tr>

                    </tfoot>

                </table>

            </div>

            @endif

        </div>

        @endif

    </div>

</x-app-layout>