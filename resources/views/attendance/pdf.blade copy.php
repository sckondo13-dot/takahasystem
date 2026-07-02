<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">

    <style>
        @font-face {
            font-family: ipa;
            font-style: normal;
            font-weight: normal;
            src: url("{{ resource_path('fonts/ipaexg.ttf') }}") format("truetype");
        }

        @page {
            margin: 15mm;
        }

        body {
            font-family: ipa;
            font-size: 10px;
            color: #222;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 15px;
        }

        .company {
            font-size: 20px;
            font-weight: bold;
        }

        .title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 10px;
            margin: 20px 0;
        }

        .info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .info-box {
            border: 1px solid #444;
            padding: 8px 15px;
            width: 280px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #efefef;
            font-size: 10px;
        }

        th,
        td {
            border: .5px solid #555;
            padding: 5px;
        }

        td {
            font-size: 9px;
        }

        .left {
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }
    </style>

</head>

<body>

    <h2>
        {{ $employee->name }}　{{ $month->format('Y年m月') }}　個人出勤簿
    </h2>
    @php

    $week=$detail->dailyReport->work_date->dayOfWeek;

    $color='';

    if($week==0){
    $color='color:red;font-weight:bold;';
    }elseif($week==6){
    $color='color:blue;font-weight:bold;';
    }

    @endphp

    <table>

        <thead>

            <tr>

                <th>日付</th>

                <th>現場</th>

                <th>作業</th>

                <th>出勤</th>

                <th>退勤</th>

                <th>休憩</th>

                <th>人工</th>

                <th>残業</th>

                <th>交通</th>

                <th>高速</th>

                <th>駐車</th>

                <th>作業手当</th>

            </tr>

        </thead>

        <tbody>

            @foreach($details as $detail)

            @php

            $start = $detail->start_time;

            $end = $detail->end_time;

            $break = 2;

            $ot = $detail->overtime_hours;

            /*
            |--------------------------------------------------------------------------
            | 休憩
            |--------------------------------------------------------------------------
            */

            if($ot >= 2.5){

            $break = 0;

            }elseif($ot >= 0.5){

            $break = max(0,2-$ot);

            }

            /*
            |--------------------------------------------------------------------------
            | 退勤時間
            |--------------------------------------------------------------------------
            */

            $workOt = max(0,$ot-2);

            if($end){

            $endTime = \Carbon\Carbon::parse($end)
            ->addMinutes($workOt*60)
            ->format('H:i');

            }else{

            $endTime='';

            }

            @endphp

            <tr>

                <td style="{{ $color }}">
                    {{ $detail->dailyReport->work_date->format('m/d') }}
                    （{{ ['日','月','火','水','木','金','土'][$week] }}）
                </td>

                <td class="left">

                    {{ $detail->dailyReport->site->name }}

                </td>

                <td>

                    {{ $detail->workType->name }}

                </td>

                <td>

                    {{ $start }}

                </td>

                <td>

                    {{ $endTime }}

                </td>

                <td>

                    {{ $break }}h

                </td>

                <td>

                    {{ $detail->man_hours }}

                </td>

                <td>

                    {{ $detail->overtime_hours }}

                </td>

                <td class="right">

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

    <div class="summary">

        <table>

            <tr>

                <th>人工合計</th>

                <th>残業合計</th>

                <th>交通費</th>

                <th>高速代</th>

                <th>駐車場代</th>

                <th>作業手当</th>

            </tr>

            <tr>

                <td>{{ $totalManHours }}</td>

                <td>{{ $totalOvertime }}</td>

                <td>{{ number_format($totalTransportation) }}</td>

                <td>{{ number_format($totalExpressway) }}</td>

                <td>{{ number_format($totalParking) }}</td>

                <td>{{ number_format($totalWorkAllowance) }}</td>

            </tr>

        </table>

    </div>

    @if($fixedAllowances->count())

    <br>

    <table>

        <tr>

            <th colspan="2">

                固定手当

            </th>

        </tr>

        @foreach($fixedAllowances as $allowance)

        <tr>

            <td>

                {{ $allowance->allowance_name }}

            </td>

            <td class="right">

                {{ number_format($allowance->amount) }}

            </td>

        </tr>

        @endforeach

        <tr>

            <th>

                合計

            </th>

            <th class="right">

                {{ number_format($fixedAllowanceTotal) }}

            </th>

        </tr>

    </table>

    @endif

    <div class="stamp">

        印鑑

    </div>

</body>

</html>