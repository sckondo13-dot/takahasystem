<!DOCTYPE html>
<html lang="ja">

<head>

    <meta charset="UTF-8">

    <style>
        @page {
            margin: 12mm;
        }

        @font-face {
            font-family: "Noto Sans JP";
            font-weight: 400;
            src: url("{{ resource_path('fonts/NotoSansJP-Regular.ttf') }}") format("truetype");
        }

        @font-face {
            font-family: "Noto Sans JP";
            font-weight: 700;
            src: url("{{ resource_path('fonts/NotoSansJP-Bold.ttf') }}") format("truetype");
        }

        body {
            font-family: "Noto Sans JP";
            font-size: 9px;
            color: #000;
        }

        h1 {
            margin: 0;
            text-align: center;
            font-size: 18px;
        }

        .company {
            text-align: center;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .info {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 15px;
        }

        .info td {
            border: 0;
            padding: 3px 6px;
            font-size: 10px;
        }

        .info .label {
            width: 60px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: .5px solid #666;
            padding: 3px;
        }

        th {
            background: #efefef;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }

        .sun {
            color: #d60000;
        }

        .sat {
            color: #0055cc;
        }

        tfoot td {
            font-weight: bold;
            background: #f3f3f3;
        }

        .items {
            background: #fafafa;
            font-size: 8px;
        }

        .summary {
            margin-top: 15px;
            width: 45%;
            float: left;
        }

        .summary td,
        .summary th {
            padding: 5px;
        }

        .stamp {
            float: right;
            margin-top: 20px;
        }

        .stamp table {
            width: 180px;
        }

        .stamp td {
            height: 55px;
            text-align: center;
        }

        .clear {
            clear: both;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }

        tr {
            page-break-inside: avoid;
        }

        .items {
            background: #fafafa;
            font-size: 8px;
        }

        .summary {
            width: 100%;
            margin-top: 15px;
        }

        .summary th {
            background: #efefef;
        }

        .stamp {
            margin-top: 18px;
            width: 220px;
            float: right;
        }

        .stamp table {
            width: 100%;
        }

        .stamp td {
            height: 55px;
        }

        .clear {
            clear: both;
        }
    </style>

</head>

<body>

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
    <table>

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

                    <br>

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

                    <strong>貸出機材</strong>

                    @foreach($data['items'] as $item)

                    ・{{ $item->name }}

                    × {{ $item->quantity }}

                    {{ $item->unit }}

                    @if(!$loop->last)

                    ／

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

            </tr>

            <tr>

                <td></td>

                <td></td>

            </tr>

        </table>

    </div>

    <div class="clear"></div>
    <div style="margin-top:25px;text-align:center;font-size:8px;color:#666;">

        以上

    </div>