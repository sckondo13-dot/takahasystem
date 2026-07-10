@extends('pdf.layout')

@push('style')

<style>
    table.report {
        table-layout: fixed;
        font-size: 8px;
    }

    table.report th {
        padding: 2px;
    }

    table.report td {
        padding: 2px;
    }

    .report .date {
        width: 46px;
    }

    .report .num {
        width: 34px;
    }

    .report .money {
        width: 56px;
    }

    .items {
        font-size: 7px;
        line-height: 1.1;
    }

    .summary {
        margin-top: 8px;
    }

    .summary th,
    .summary td {
        padding: 4px;
    }

    .summary td {
        font-size: 9px;
    }

    .stamp {
        width: 150px;
        float: right;
        margin-top: 8px;
    }

    .stamp td {
        height: 45px;
    }
</style>

@endpush
@section('content')

<table class="info">

    <tr>

        <td class="label">
            現場
        </td>

        <td>
            {{ $site->name }}
        </td>

        <td class="label">
            元請
        </td>

        <td>
            {{ $site->client->name }}
        </td>

        <td class="label">
            対象月
        </td>

        <td>
            {{ $month->format('Y年m月') }}
        </td>

    </tr>

</table>
<table class="report">

    <thead>

        <tr>

            <th style="width:55px;">日付</th>

            @foreach($visibleTypes as $type)
            <th>{{ str_replace('工','',$type) }}</th>
            @endforeach

            <th style="width:30px;">人工</th>
            @foreach($visibleTypes as $type)
            <th style="width:55px;">
                {{ str_replace('工','',$type) }}単価
            </th>
            @endforeach

            <th style="width:85px;">合計</th>

            <th style="width:45px;">残業</th>

            <th style="width:70px;">交通</th>

            <th style="width:70px;">高速</th>

            <th style="width:70px;">駐車</th>

        </tr>

    </thead>

    <tbody>

        @foreach($dates as $date)

        @php

        $key = $date->format('Y-m-d');

        $data = $reportMap[$key] ?? null;

        $day = $date->dayOfWeek;

        @endphp

        <tr>

            <td class="center
                @if($day==0) sun @endif
                @if($day==6) sat @endif">

                {{ $date->format('n/j') }}

                （{{ ['日','月','火','水','木','金','土'][$day] }}）

            </td>

            @foreach($visibleTypes as $type)

            <td class="center">

                {{ $data[$type] ?? '' }}

            </td>

            @endforeach

            <td class="center">

                {{ $data['total_man'] ?? '' }}

            </td>

            @foreach($visibleTypes as $type)
            <td class="right">

                @php
                $priceMap = [
                '解体工' => $site->client->demolition_unit_price,
                '重機' => $site->client->heavy_equipment_unit_price,
                '重機２' => $site->client->heavy_equipment2_unit_price,
                'ガス工' => $site->client->gas_unit_price ?? 0,
                'はつり' => $site->client->chipping_unit_price,
                '石綿' => $site->client->asbestos_unit_price,
                'トラック' => $site->client->truck_unit_price,
                ];
                @endphp

                {{ number_format($priceMap[$type] ?? 0) }}

            </td>
            @endforeach

            <td class="right">

                @if($data)

                {{ number_format($data['sales']) }}

                @endif

            </td>

            <td class="center">

                {{ $data['overtime'] ?? '' }}

            </td>

            <td class="right">

                @if($data)

                {{ number_format($data['transportation']) }}

                @endif

            </td>

            <td class="right">

                @if($data)

                {{ number_format($data['expressway']) }}

                @endif

            </td>

            <td class="right">

                @if($data)

                {{ number_format($data['parking']) }}

                @endif

            </td>

        </tr>

        @endforeach

    </tbody>
    <tfoot>

        <tr style="font-weight:bold;background:#eee;">

            <td>合計</td>

            @foreach($visibleTypes as $type)

            <td class="center">

                {{ number_format($typeTotals[$type],1) }}

            </td>

            @endforeach

            <td class="center">{{ number_format($totalManHours,1) }}</td>

            @foreach($visibleTypes as $type)
            <td class="right">
                {{ number_format($typeSalesTotals[$type]) }}
            </td>
            @endforeach

            <td class="right">{{ number_format($totalSales) }}</td>

            <td class="center">{{ number_format($totalOvertime,1) }}</td>

            <td class="right">{{ number_format($totalTransportation) }}</td>

            <td class="right">{{ number_format($totalExpressway) }}</td>

            <td class="right">{{ number_format($totalParking) }}</td>

        </tr>

    </tfoot>

</table>
<br>
@if($itemList->count())

<br>

<table>

    <thead>

        <tr>

            <th style="width:70px;">日付</th>
            <th>貸出機材・資材</th>
            <th style="width:90px;">数量</th>

        </tr>

    </thead>

    <tbody>

        @foreach($itemList as $item)

        <tr>

            <td class="center">

                {{ $item['date']->format('n/j') }}

            </td>

            <td>

                {{ $item['name'] }}

            </td>

            <td class="center">

                {{ number_format($item['quantity']) }}
                {{ $item['unit'] }}

            </td>

        </tr>

        @endforeach

    </tbody>

</table>

@endif
@if(count($itemTotals))

<br>

<table>

    <thead>

        <tr>

            <th colspan="2">

                貸出・資材 合計

            </th>

        </tr>

    </thead>

    <tbody>

        @foreach($itemTotals as $item)

        <tr>

            <td>

                {{ $item['name'] }}

            </td>

            <td class="center">

                {{ number_format($item['quantity']) }}
                {{ $item['unit'] }}

            </td>

        </tr>

        @endforeach

    </tbody>

</table>

@endif
<table class="summary">

    <tr>

        <th>合計人工</th>

        <th>売上</th>

        <th>残業</th>

        <th>交通費</th>

        <th>高速代</th>

        <th>駐車場代</th>

    </tr>

    <tr>

        <td class="center">

            {{ number_format($totalManHours,1) }}

        </td>

        <td class="right">

            {{ number_format($totalSales) }}

        </td>

        <td class="center">

            {{ number_format($totalOvertime,1) }}

        </td>

        <td class="right">

            {{ number_format($totalTransportation) }}

        </td>

        <td class="right">

            {{ number_format($totalExpressway) }}

        </td>

        <td class="right">

            {{ number_format($totalParking) }}

        </td>

    </tr>

</table>
@endsection