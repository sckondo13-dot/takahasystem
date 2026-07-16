@extends('pdf.layout')

@push('style')
<style>
    table.report {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 8px;
    }

    .report th,
    .report td {
        border: 1px solid #000;
        padding: 3px;
    }

    .report th {
        background: #eeeeee;
    }

    .center {
        text-align: center;
    }

    .money {
        text-align: right;
    }

    tfoot {
        font-weight: bold;
        background: #eeeeee;
    }

    .sun {
        color: #d00000;
    }

    .sat {
        color: #0047d4;
    }

    .title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .info {
        margin-bottom: 10px;
    }
</style>
@endpush

@section('content')

<div class="title">
    ニセコ木造解体　現場別月報
</div>

<table class="info">

    <tr>

        <td width="120">対象月</td>

        <td>{{ $month->format('Y年m月') }}</td>

        <td width="120">元請</td>

        <td>{{ $client->name }}</td>

    </tr>

</table>

<table class="report">

    <thead>

        <tr>

            <th width="55">
                日付
            </th>

            @foreach($visibleSites as $site)

            <th>

                {!! implode('<br>', mb_str_split(
                preg_replace('/^木造解体：/u','',$site->name),
                4
                )) !!}
            </th>

            @endforeach

            <th width="45">
                人工
            </th>

            <th width="60">
                単価
            </th>

            <th width="75">
                売上
            </th>

            <th width="60">
                交通費
            </th>

        </tr>

    </thead>

    <tbody>

        @foreach($rows as $row)

        @php

        $day=$row['date']->dayOfWeek;

        @endphp

        <tr>

            <td class="center
@if($day==0) sun @endif
@if($day==6) sat @endif">

                {{ $row['date']->format('n/j') }}
                <br>
                （{{ ['日','月','火','水','木','金','土'][$day] }}）

            </td>

            @foreach($visibleSites as $site)

            <td class="center">

                @if(isset($row['sites'][$site->id]))

                {{ number_format($row['sites'][$site->id]['man'],1) }}

                @endif

            </td>

            @endforeach

            <td class="center">

                {{ number_format($row['total_man'],1) }}

            </td>

            <td class="money">

                @if($row['total_man']>0)

                {{ number_format($client->demolition_unit_price) }}

                @endif

            </td>

            <td class="money">

                @if($row['sales']>0)

                {{ number_format($row['sales']) }}

                @endif

            </td>

            <td class="money">

                @if($row['transportation']>0)

                {{ number_format($row['transportation']) }}

                @endif

            </td>

        </tr>

        @endforeach

    </tbody>

    <tfoot>

        <tr>

            <td>

                合計

            </td>

            @foreach($visibleSites as $site)

            <td class="center">

                {{ number_format($siteTotals[$site->id],1) }}

            </td>

            @endforeach

            <td class="center">

                {{ number_format($totalManHours,1) }}

            </td>

            <td class="money">

                {{ number_format($client->demolition_unit_price) }}

            </td>

            <td class="money">

                {{ number_format($totalSales) }}

            </td>

            <td class="money">

                {{ number_format($totalTransportation) }}

            </td>

        </tr>

    </tfoot>

</table>

@endsection