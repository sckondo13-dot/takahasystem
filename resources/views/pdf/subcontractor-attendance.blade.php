@extends('pdf.layout')

@section('content')

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

        月明細

    </div>

    <table class="info">

        <tr>

            <td width="50%">

                <b>会社名：</b>

                {{ $subcontractor->name }}

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

                <th width="18%">現場</th>

                <th width="9%">作業</th>

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

            $start = '';

            if ($detail->start_time) {
            $start = \Carbon\Carbon::parse($detail->start_time)
            ->format('H:i');
            }

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


                </tbody>

            </table>

        </div>

        <div class="footer-right">

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

    @endsection