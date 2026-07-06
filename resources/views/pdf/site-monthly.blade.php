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

<div class="company">
    髙橋興業
</div>

<h1>
    現場別月報
</h1>

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

            <th style="width:45px;">解体</th>

            <th style="width:45px;">重機</th>

            <th style="width:45px;">重機2</th>

            <th style="width:45px;">ガス</th>

            <th style="width:45px;">はつり</th>

            <th style="width:45px;">石綿</th>

            <th style="width:45px;">トラック</th>

            <th style="width:45px;">人工</th>

            <th style="width:85px;">売上</th>

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

            <td class="center">{{ $data['解体工'] ?? '' }}</td>

            <td class="center">{{ $data['重機'] ?? '' }}</td>

            <td class="center">{{ $data['重機２'] ?? '' }}</td>

            <td class="center">{{ $data['ガス工'] ?? '' }}</td>

            <td class="center">{{ $data['はつり'] ?? '' }}</td>

            <td class="center">{{ $data['石綿'] ?? '' }}</td>

            <td class="center">{{ $data['トラック'] ?? '' }}</td>

            <td class="center">

                {{ $data['total_man'] ?? '' }}

            </td>

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

        @if($data && $data['items']->count())

        <tr>

            <td></td>

            <td colspan="13" class="items">

                貸出：

                @foreach($data['items'] as $item)

                {{ $item->name }}

                ×

                {{ number_format($item->quantity) }}

                {{ $item->unit }}

                @if(!$loop->last)

                、

                @endif

                @endforeach

            </td>

        </tr>

        @endif

        @endforeach

    </tbody>

</table>
<br>

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
<div class="stamp">

    <table>

        <tr>

            <th>担当</th>

            <th>確認</th>

            <th>承認</th>

        </tr>

        <tr>

            <td style="height:45px;"></td>

            <td></td>

            <td></td>

        </tr>

    </table>

</div>

<div class="clear"></div>
@endsection