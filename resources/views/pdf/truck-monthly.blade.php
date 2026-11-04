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
        width: 100%;
        margin-bottom: 10px;
        font-size: 10px;
    }

    .site-name {
        word-wrap: break-word;
        overflow-wrap: break-word;
        font-size: 7px;
    }

    .empty {
        text-align: center;
        padding: 15px;
    }
</style>
@endpush

@section('content')

@php
$clientName = $visibleSites->first()?->client?->name
?? '北海道リサイクラー';
@endphp

<div class="title">
    北海道リサイクラー トラック明細
</div>

{{-- 基本情報 --}}
<table class="info">

    <tr>
        <td width="80">対象月</td>

        <td>
            {{ $month->format('Y年m月') }}
        </td>

        <td width="80">元請</td>

        <td>
            {{ $clientName }}
        </td>
    </tr>

</table>

<table class="report">

    <thead>

        <tr>

            <th width="45">
                日付
            </th>

            @foreach($visibleSites as $site)

            <th>

                <div class="site-name">
                    {{ str_replace('(トラック)', '', $site->name) }}
                </div>

                <div style="font-size: 7px; font-weight: normal;">
                    単価：{{ number_format($site->client->truck_unit_price ?? 0) }}円
                </div>

            </th>

            @endforeach

            <th width="40">
                人工
            </th>

            <th width="40">
                残業
            </th>

            <th width="55">
                売上
            </th>

            <th width="50">
                高速代
            </th>

        </tr>

    </thead>

    <tbody>

        @foreach($rows as $row)

        @php
        $day = $row['date']->dayOfWeek;
        @endphp

        <tr>

            {{-- 日付 --}}
            <td class="center
                @if($day == 0) sun @endif
                @if($day == 6) sat @endif">

                {{ $row['date']->format('n/j') }}
                （{{ ['日','月','火','水','木','金','土'][$day] }}）

            </td>

            {{-- 現場ごとの人工 --}}
            @foreach($visibleSites as $site)

            <td class="center">

                @if(isset($row['sites'][$site->id]))

                {{ number_format($row['sites'][$site->id]['man'], 1) }}

                @endif

            </td>

            @endforeach

            {{-- 人工合計 --}}
            <td class="center">

                {{ number_format($row['total_man'], 1) }}

            </td>

            {{-- 残業 --}}
            <td class="center">

                @if($row['overtime'] > 0)

                {{ number_format($row['overtime'], 2) }}

                @endif

            </td>

            {{-- 売上 --}}
            <td class="money">

                @if($row['sales'] > 0)

                {{ number_format($row['sales']) }}

                @endif

            </td>

            {{-- 高速代 --}}
            <td class="money">

                @if($row['expressway'] > 0)

                {{ number_format($row['expressway']) }}

                @endif

            </td>

        </tr>

        @endforeach

    </tbody>

    {{-- 月間合計 --}}
    <tfoot>

        <tr>

            <td class="center">
                合計
            </td>

            @foreach($visibleSites as $site)

            <td class="center">

                {{ number_format($siteTotals[$site->id] ?? 0, 1) }}

            </td>

            @endforeach

            <td class="center">

                {{ number_format($totalManHours, 1) }}

            </td>

            <td class="center">

                {{ number_format($totalOvertime, 2) }}

            </td>

            <td class="money">

                {{ number_format($totalSales) }}

            </td>

            <td class="money">

                {{ number_format($totalExpressway) }}

            </td>


        </tr>

    </tfoot>

</table>

@if($visibleSites->isEmpty())

<div class="empty">
    選択した月にトラックの作業実績はありません。
</div>

@endif

@endsection