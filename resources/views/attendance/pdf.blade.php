<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 8mm 10mm;
        }

        @font-face {
            font-family: ipa;
            src: url("{{ resource_path('fonts/ipaexg.ttf') }}") format("truetype");
        }

        body {
            font-family: ipa;
            font-size: 10px;
            color: #222;
            margin: 0;
            padding: 0;
            zoom: 0.93;
        }

        * {
            box-sizing: border-box;
        }

        .header {

            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 12px;
            padding-bottom: 8px;

        }

        .company {

            float: left;
            font-size: 18px;
            font-weight: bold;

        }

        .created {

            float: right;
            font-size: 10px;

        }

        .clear {

            clear: both;

        }

        .title {

            text-align: center;
            font-size: 26px;
            font-weight: bold;
            letter-spacing: 8px;
            margin: 15px 0;

        }

        .info {

            width: 100%;
            margin-bottom: 15px;

        }

        .info td {

            border: none;
            padding: 3px;

        }

        table {

            width: 100%;
            border-collapse: collapse;

        }

        thead {

            display: table-header-group;

        }

        tbody tr:nth-child(even) {

            background: #fafafa;

        }

        tr {

            page-break-inside: avoid;

        }

        td {

            vertical-align: middle;

        }

        th {

            font-weight: bold;

            letter-spacing: 1px;

        }

        th {

            background: #efefef;
            font-size: 10px;

        }

        td {

            font-size: 9px;

        }

        th,
        td {

            border: .5px solid #555;
            padding: 4px;

        }

        .left {

            text-align: left;

            padding-left: 5px;

            word-break: break-all;

        }

        .center {

            text-align: center;

        }

        .right {

            text-align: right;

        }

        .sun {

            color: red;
            font-weight: bold;

        }

        .sat {

            color: blue;
            font-weight: bold;

        }

        .summary {

            margin-top: 18px;

        }

        .summary table th {

            background: #f5f5f5;

        }

        .footer {

            margin-top: 20px;
            width: 100%;
            page-break-inside: avoid;

        }

        .footer-left {

            width: 60%;
            float: left;

        }

        .footer-right {

            width: 35%;
            float: right;

        }

        .stamp-area {

            margin-top: 25px;
            text-align: right;

        }

        .stamp {

            width: 90px;
            height: 90px;
            border: 1px solid #000;
            display: inline-block;
            margin-left: 20px;
            text-align: center;
            line-height: 90px;
            font-size: 12px;

        }

        .money {

            text-align: right;

            white-space: nowrap;

        }
    </style>

</head>

<body>

    <div class="header">

        <div class="company">

            髙橋興業

        </div>

        <div class="created">

            作成日：
            {{ now()->format('Y/m/d') }}

        </div>

        <div class="clear"></div>

    </div>

    <div class="title">

        個　人　出　勤　簿

    </div>

    <table class="info">

        <tr>

            <td width="50%">

                <b>氏名：</b>

                {{ $employee->name }}

            </td>

            <td width="50%" class="right">

                <b>対象月：</b>

                {{ $month->format('Y年m月') }}

            </td>

        </tr>

    </table>
    <table>
        <thead>

            <tr>

                <th width="10%">日付</th>

                <th width="22%">現場</th>

                <th width="10%">作業</th>

                <th width="7%">出勤</th>

                <th width="7%">退勤</th>

                <th width="6%">休憩</th>

                <th width="6%">人工</th>

                <th width="6%">残業</th>

                <th width="8%">交通費</th>

                <th width="8%">高速代</th>

                <th width="8%">駐車場</th>

                <th width="8%">手当</th>

            </tr>

        </thead>

        <tbody>

            @foreach($details as $detail)

            @php

            $week = $detail->dailyReport->work_date->dayOfWeek;

            $weekName=['日','月','火','水','木','金','土'][$week];

            $rowClass='';

            if($week==0){

            $rowClass='sun';

            }elseif($week==6){

            $rowClass='sat';

            }

            /*
            |--------------------------------------------------------------------------
            | 出勤・退勤
            |--------------------------------------------------------------------------
            */

            $start=$detail->start_time;

            $end=$detail->end_time;

            /*
            |--------------------------------------------------------------------------
            | 人工
            |--------------------------------------------------------------------------
            */

            $man=$detail->man_hours;

            /*
            |--------------------------------------------------------------------------
            | 残業
            |--------------------------------------------------------------------------
            */

            $ot=$detail->overtime_hours;

            /*
            |--------------------------------------------------------------------------
            | 休憩時間
            |--------------------------------------------------------------------------
            */

            if($man < 1){

                $break=0;

                }else{

                if($ot==0){

                $break=2;

                }elseif($ot==1){

                $break=1;

                }elseif($ot>=2){

                $break=0;

                }else{

                $break=max(0,2-$ot);

                }

                }

                /*
                |--------------------------------------------------------------------------
                | 退勤時間
                |--------------------------------------------------------------------------
                */

                $endTime='';

                if($end){

                $endCarbon=\Carbon\Carbon::parse($end);

                if($ot>2){

                $endCarbon->addHours($ot-2);

                }

                $endTime=$endCarbon->format('H:i');

                }

                @endphp

                <tr>

                    <td class="center {{ $rowClass }}">

                        {{ $detail->dailyReport->work_date->format('m/d') }}

                        （{{ $weekName }}）

                    </td>

                    <td class="left">

                        {{ $detail->dailyReport->site->name }}

                    </td>

                    <td class="center">

                        {{ $detail->workType->name }}

                    </td>

                    <td class="center">

                        {{ $start }}

                    </td>

                    <td class="center">

                        {{ $endTime }}

                    </td>

                    <td class="center">

                        {{ $break }}h

                    </td>

                    <td class="center">

                        {{ number_format($detail->man_hours,1) }}

                    </td>

                    <td class="center">

                        {{ number_format($detail->overtime_hours,1) }}

                    </td>

                    <td class="money">

                        {{ number_format($detail->transportation_cost) }}

                    </td>

                    <td class="right">

                        {{ number_format($detail->expressway_cost) }}

                    </td>

                    <td class="right">

                        {{ number_format($detail->parking_cost) }}

                    </td>

                    <td class="right">

                        {{ number_format($detail->work_allowance) }}

                    </td>

                </tr>

                @endforeach

        </tbody>

    </table>

    <div class="footer">

        <div class="footer-left">

            <table>

                <thead>

                    <tr>

                        <th colspan="2">

                            月間集計

                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td class="left">

                            人工合計

                        </td>

                        <td class="right">

                            {{ number_format($totalManHours,1) }}

                        </td>

                    </tr>

                    <tr>

                        <td class="left">

                            残業合計

                        </td>

                        <td class="right">

                            {{ number_format($totalOvertime,1) }} h

                        </td>

                    </tr>

                    <tr>

                        <td class="left">

                            交通費

                        </td>

                        <td class="right">

                            {{ number_format($totalTransportation) }}

                        </td>

                    </tr>

                    <tr>

                        <td class="left">

                            高速代

                        </td>

                        <td class="right">

                            {{ number_format($totalExpressway) }}

                        </td>

                    </tr>

                    <tr>

                        <td class="left">

                            駐車場代

                        </td>

                        <td class="right">

                            {{ number_format($totalParking) }}

                        </td>

                    </tr>

                    <tr>

                        <td class="left">

                            作業手当

                        </td>

                        <td class="right">

                            {{ number_format($totalWorkAllowance) }}

                        </td>

                    </tr>

                    <tr style="font-weight:bold;">

                        <td class="left">

                            各種手当合計

                        </td>

                        <td class="right">

                            {{ number_format(
                            $totalTransportation
                            + $totalExpressway
                            + $totalParking
                            + $totalWorkAllowance
                            + $fixedAllowanceTotal
                        ) }}

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <div class="footer-right">

            @if($fixedAllowances->count())

            <table>

                <thead>

                    <tr>

                        <th colspan="2">

                            固定手当

                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($fixedAllowances as $allowance)

                    <tr>

                        <td class="left">

                            {{ $allowance->allowance_name }}

                        </td>

                        <td class="right">

                            {{ number_format($allowance->amount) }}

                        </td>

                    </tr>

                    @endforeach

                    <tr style="font-weight:bold;">

                        <td>

                            合計

                        </td>

                        <td class="right">

                            {{ number_format($fixedAllowanceTotal) }}

                        </td>

                    </tr>

                </tbody>

            </table>

            @endif

        </div>

        <div style="clear:both;"></div>

    </div>
    <div class="stamp-area">

        <table style="width:300px;float:right;">

            <tr>

                <th>

                    社印

                </th>

                <th>

                    確認

                </th>

                <th>

                    本人

                </th>

            </tr>

            <tr>

                <td style="height:80px;"></td>

                <td></td>

                <td></td>

            </tr>

        </table>

    </div>

</body>

</html>