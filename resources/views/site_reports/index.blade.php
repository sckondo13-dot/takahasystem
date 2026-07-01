<x-app-layout>

    <div class="max-w-7xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-5">
            現場別月報
        </h1>

        <form method="GET" class="flex gap-3 mb-5">

            <select
                name="site_id"
                class="border rounded p-2">

                <option value="">
                    現場選択
                </option>

                @foreach($sites as $site)

                <option
                    value="{{ $site->id }}"
                    {{ $siteId==$site->id?'selected':'' }}>

                    {{ $site->name }}

                </option>

                @endforeach

            </select>

            <input
                type="month"
                name="month"
                value="{{ $month->format('Y-m') }}"
                class="border rounded p-2">

            <button class="bg-blue-600 text-white px-5 rounded">
                表示
            </button>

        </form>

        <table class="w-full border">

            <thead class="bg-gray-100">

                <tr>

                    <th>日付</th>

                    <th>社員</th>

                    <th>作業</th>

                    <th>人工</th>

                    <th>単価</th>

                    <th>売上</th>

                    <th>残業</th>

                    <th>交通費</th>

                    <th>高速</th>

                    <th>駐車場</th>

                    <th>備考</th>

                </tr>

            </thead>

            <tbody>

                @foreach($reports as $report)

                @foreach($report->details as $detail)

                <tr>

                    <td>

                        {{ $report->work_date->format('Y/m/d') }}

                    </td>

                    <td>

                        @if($detail->employee)

                        {{ $detail->employee->name }}

                        @else

                        {{ $detail->subcontractor->name }}

                        @endif

                    </td>

                    <td>

                        {{ $detail->workType->name }}

                    </td>

                    <td>

                        {{ $detail->man_hours }}

                    </td>

                    <td>

                        {{ number_format($detail->unit_price) }}

                    </td>

                    <td>

                        {{ number_format($detail->sales) }}

                    </td>

                    <td>

                        {{ $detail->overtime_hours }}

                    </td>

                    <td>

                        {{ number_format($detail->transportation_cost) }}

                    </td>

                    <td>

                        {{ number_format($detail->expressway_cost) }}

                    </td>

                    <td>

                        {{ number_format($detail->parking_cost) }}

                    </td>

                    <td>

                        {{ $detail->note }}

                    </td>

                </tr>

                @endforeach

                @endforeach

            </tbody>

        </table>
        @if($report->freeItems->count())

        <tr>

            <td colspan="11">

                <div class="bg-gray-50 p-3 rounded">

                    <b>貸出機材</b>

                    <ul>

                        @foreach($report->freeItems as $item)

                        <li>

                            {{ $item->item_name }}

                            ×

                            {{ $item->quantity }}

                        </li>

                        @endforeach

                    </ul>

                </div>

            </td>

        </tr>

        @endif

        <tfoot class="bg-gray-100 font-bold">

            <tr>

                <td colspan="3">

                    合計

                </td>

                <td>

                    {{ $totalManHours }}

                </td>

                <td></td>

                <td>

                    {{ number_format($totalSales) }}

                </td>

                <td></td>

                <td>

                    {{ number_format($totalTransportation) }}

                </td>

                <td>

                    {{ number_format($totalExpressway) }}

                </td>

                <td>

                    {{ number_format($totalParking) }}

                </td>

                <td></td>

            </tr>

        </tfoot>