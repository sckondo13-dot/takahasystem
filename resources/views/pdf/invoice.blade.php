@extends('pdf.layout')

@push('style')
<style>
    body {
        font-family: 'Noto Sans JP';
        font-size: 10px;
        color: #222;
    }

    .invoice-title {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
        letter-spacing: 5px;
    }

    .top-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .top-table td {
        vertical-align: top;
    }

    .client-area {
        width: 55%;
        font-size: 14px;
        font-weight: bold;
    }

    .client-name {
        border-bottom: 1px solid #333;
        padding: 8px 5px;
        font-size: 16px;
    }

    .company-area {
        width: 45%;
        text-align: right;
        line-height: 1.7;
    }

    .company-name {
        font-size: 15px;
        font-weight: bold;
    }

    .meta-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .meta-table th {
        width: 90px;
        background: #f3f3f3;
        border: 1px solid #999;
        padding: 5px;
        text-align: center;
    }

    .meta-table td {
        border: 1px solid #999;
        padding: 5px;
    }

    .total-area {
        margin: 15px 0;
        border: 2px solid #333;
        padding: 10px 15px;
        width: 55%;
    }

    .total-label {
        font-size: 11px;
    }

    .total-price {
        font-size: 22px;
        font-weight: bold;
        text-align: right;
    }

    table.details {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        margin-top: 10px;
    }

    table.details th {
        background: #e9e9e9;
        border: 1px solid #777;
        padding: 6px 4px;
        text-align: center;
        font-weight: bold;
    }

    table.details td {
        border: 1px solid #777;
        padding: 6px 4px;
        height: 24px;
    }

    .center {
        text-align: center;
    }

    .right {
        text-align: right;
    }

    .subtotal-area {
        width: 45%;
        margin-left: auto;
        margin-top: 10px;
    }

    .subtotal-area table {
        width: 100%;
        border-collapse: collapse;
    }

    .subtotal-area th {
        background: #f3f3f3;
        border: 1px solid #777;
        padding: 6px;
        text-align: left;
    }

    .subtotal-area td {
        border: 1px solid #777;
        padding: 6px;
        text-align: right;
    }

    .bank-area {
        margin-top: 20px;
        width: 60%;
    }

    .section-title {
        font-weight: bold;
        font-size: 12px;
        border-left: 4px solid #333;
        padding-left: 7px;
        margin-bottom: 5px;
    }

    .bank-table {
        width: 100%;
        border-collapse: collapse;
    }

    .bank-table th {
        background: #f3f3f3;
        border: 1px solid #777;
        padding: 5px;
        width: 90px;
    }

    .bank-table td {
        border: 1px solid #777;
        padding: 5px;
    }

    .remarks {
        margin-top: 20px;
    }

    .remarks-box {
        border: 1px solid #777;
        min-height: 60px;
        padding: 8px;
    }

    .small {
        font-size: 8px;
    }
</style>
@endpush


@section('content')

{{-- タイトル --}}
<div class="invoice-title">
    請求書
</div>


{{-- 請求先・請求元 --}}
<table class="top-table">

    <tr>

        {{-- 請求先 --}}
        <td class="client-area">

            <div class="client-name">

                {{ $invoice->client->name }} 御中

            </div>

        </td>


        {{-- 請求元 --}}
        <td class="company-area">

            <div class="company-name">

                {{ $invoice->company->name }}

            </div>

            @if($invoice->company->postal_code)
            〒{{ $invoice->company->postal_code }}<br>
            @endif

            {{ $invoice->company->address }}<br>

            TEL：{{ $invoice->company->tel }}<br>

            @if($invoice->company->fax)
            FAX：{{ $invoice->company->fax }}<br>
            @endif

            @if($invoice->company->email)
            {{ $invoice->company->email }}<br>
            @endif

            @if($invoice->company->registration_number)

            <span class="small">
                登録番号：{{ $invoice->company->registration_number }}
            </span>

            @endif

        </td>

    </tr>

</table>


{{-- 請求情報 --}}
<table class="meta-table">

    <tr>

        <th>
            請求書番号
        </th>

        <td>
            {{ $invoice->invoice_no }}
        </td>

        <th>
            請求日
        </th>

        <td>
            {{ optional($invoice->invoice_date)->format('Y年m月d日') }}
        </td>

    </tr>

    <tr>

        <th>
            支払期限
        </th>

        <td>
            {{ optional($invoice->payment_due)->format('Y年m月d日') }}
        </td>

        <th>
            件名
        </th>

        <td>
            {{ $invoice->title }}
        </td>

    </tr>

</table>


{{-- 現場 --}}
@if($invoice->site)

<table class="meta-table">

    <tr>

        <th>
            現場名
        </th>

        <td>
            {{ $invoice->site->name }}
        </td>

    </tr>

</table>

@endif


{{-- 合計金額 --}}
<div class="total-area">

    <div class="total-label">
        ご請求金額
    </div>

    <div class="total-price">
        ￥{{ number_format($invoice->total) }}
    </div>

</div>


{{-- 明細 --}}
<div class="section-title">
    請求明細
</div>

<table class="details">

    <thead>

        <tr>

            <th width="12%">
                品番
            </th>

            <th width="38%">
                品名
            </th>

            <th width="10%">
                数量
            </th>

            <th width="10%">
                単位
            </th>

            <th width="15%">
                単価
            </th>

            <th width="15%">
                金額
            </th>

        </tr>

    </thead>

    <tbody>

        @forelse($invoice->details as $detail)

        <tr>

            <td class="center">
                -
            </td>

            <td>
                {{ $detail->description }}
            </td>

            <td class="right">
                {{ number_format($detail->quantity, 2) }}
            </td>

            <td class="center">
                {{ $detail->unit }}
            </td>

            <td class="right">
                ￥{{ number_format($detail->unit_price) }}
            </td>

            <td class="right">
                ￥{{ number_format($detail->amount) }}
            </td>

        </tr>

        @empty

        <tr>

            <td colspan="6" class="center">
                明細なし
            </td>

        </tr>

        @endforelse

    </tbody>

</table>


{{-- 金額 --}}
<div class="subtotal-area">

    <table>

        <tr>

            <th>
                小計
            </th>

            <td>
                ￥{{ number_format($invoice->subtotal) }}
            </td>

        </tr>

        <tr>

            <th>
                消費税
            </th>

            <td>
                ￥{{ number_format($invoice->tax) }}
            </td>

        </tr>

        <tr>

            <th>
                合計
            </th>

            <td>
                <strong>
                    ￥{{ number_format($invoice->total) }}
                </strong>
            </td>

        </tr>

    </table>

</div>


{{-- 振込先 --}}
<div class="bank-area">

    <div class="section-title">
        お振込先
    </div>

    <table class="bank-table">

        <tr>

            <th>
                銀行名
            </th>

            <td>
                {{ $invoice->company->bank_name }}
            </td>

        </tr>

        <tr>

            <th>
                支店名
            </th>

            <td>
                {{ $invoice->company->branch_name }}
            </td>

        </tr>

        <tr>

            <th>
                口座種別
            </th>

            <td>
                {{ $invoice->company->account_type }}
            </td>

        </tr>

        <tr>

            <th>
                口座番号
            </th>

            <td>
                {{ $invoice->company->account_number }}
            </td>

        </tr>

        <tr>

            <th>
                口座名義
            </th>

            <td>
                {{ $invoice->company->account_name }}
            </td>

        </tr>

    </table>

</div>


{{-- 備考 --}}
<div class="remarks">

    <div class="section-title">
        備考
    </div>

    <div class="remarks-box">

        {!! nl2br(e($invoice->remarks)) !!}

    </div>

</div>

@endsection