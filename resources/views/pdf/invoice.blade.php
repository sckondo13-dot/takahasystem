@extends('pdf.layout')

@push('style')
<style>
    body {
        font-family: 'Noto Sans JP';
        font-size: 10px;
        color: #222;
    }

    .invoice-wrapper {
        width: 100%;
    }

    /* =========================
       タイトル
    ========================= */
    .invoice-title {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
        letter-spacing: 5px;
    }

    /* =========================
       ヘッダー
    ========================= */
    .header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .client-area {
        width: 55%;
        vertical-align: top;
    }

    .company-area {
        width: 45%;
        vertical-align: top;
        text-align: right;
    }

    .client-label {
        font-size: 11px;
        margin-bottom: 4px;
    }

    .client-name {
        font-size: 16px;
        font-weight: bold;
        border-bottom: 1px solid #222;
        display: inline-block;
        padding-bottom: 3px;
    }

    .company-name {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .company-info {
        line-height: 1.5;
    }

    .registration-number {
        margin-top: 4px;
        font-weight: bold;
    }

    /* =========================
       請求日など
    ========================= */
    .invoice-info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .invoice-info-table th,
    .invoice-info-table td {
        border: 1px solid #999;
        padding: 6px 8px;
    }

    .invoice-info-table th {
        background: #f3f3f3;
        width: 15%;
        text-align: center;
        font-weight: bold;
    }

    .invoice-info-table td {
        width: 35%;
    }

    /* =========================
       合計金額
    ========================= */
    .total-box {
        width: 100%;
        border: 2px solid #222;
        margin: 15px 0 20px;
        padding: 10px 15px;
        box-sizing: border-box;
    }

    .total-label {
        font-size: 12px;
        font-weight: bold;
    }

    .total-price {
        text-align: right;
        font-size: 20px;
        font-weight: bold;
    }

    /* =========================
       明細
    ========================= */
    .section-title {
        font-size: 12px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .details {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .details th,
    .details td {
        border: 1px solid #999;
        padding: 6px 5px;
    }

    .details th {
        background: #f3f3f3;
        text-align: center;
        font-weight: bold;
    }

    .details td {
        vertical-align: middle;
    }

    /* 列幅 */
    .details .site-col {
        width: 19%;
    }

    .details .name-col {
        width: 25%;
    }

    .details .quantity-col {
        width: 8%;
    }

    .details .unit-col {
        width: 8%;
    }

    .details .tax-col {
        width: 10%;
    }

    .details .unit-price-col {
        width: 14%;
    }

    .details .amount-col {
        width: 16%;
    }

    .details .center {
        text-align: center;
    }

    .details .right {
        text-align: right;
    }

    /* =========================
       集計
    ========================= */
    .summary-area {
        width: 45%;
        margin-left: auto;
        margin-top: 15px;
    }

    .summary-area table {
        width: 100%;
        border-collapse: collapse;
    }

    .summary-area th,
    .summary-area td {
        border: 1px solid #999;
        padding: 6px 8px;
    }

    .summary-area th {
        width: 45%;
        background: #f3f3f3;
        text-align: center;
    }

    .summary-area td {
        text-align: right;
    }

    .summary-total th,
    .summary-total td {
        font-size: 12px;
        font-weight: bold;
    }

    /* =========================
       下部
    ========================= */
    .bottom-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 25px;
    }

    .bank-area {
        width: 48%;
        vertical-align: top;
    }

    .remarks-area {
        width: 48%;
        vertical-align: top;
    }

    .bank-table {
        width: 100%;
        border-collapse: collapse;
    }

    .bank-table th,
    .bank-table td {
        border: 1px solid #999;
        padding: 5px 6px;
    }

    .bank-table th {
        width: 35%;
        background: #f3f3f3;
        text-align: center;
    }

    .remarks-box {
        border: 1px solid #999;
        min-height: 90px;
        padding: 8px;
        box-sizing: border-box;
    }

    .small {
        font-size: 9px;
    }

    .nowrap {
        white-space: nowrap;
    }
</style>
@endpush


@section('content')

<div class="invoice-wrapper">

    {{-- =========================
         タイトル
    ========================= --}}
    <div class="invoice-title">
        請求書
    </div>


    {{-- =========================
         宛先・発行者
    ========================= --}}
    <table class="header-table">
        <tr>

            {{-- 宛先 --}}
            <td class="client-area">

                <div class="client-label">
                    請求先
                </div>

                <div class="client-name">
                    {{ $invoice->client->name ?? '' }} 御中
                </div>

            </td>


            {{-- 発行者 --}}
            <td class="company-area">

                <div class="company-name">
                    {{ $invoice->company->name ?? '' }}
                </div>

                <div class="company-info">

                    @if(!empty($invoice->company->postal_code))
                        〒{{ $invoice->company->postal_code }}<br>
                    @endif

                    {{ $invoice->company->address ?? '' }}<br>

                    @if(!empty($invoice->company->tel))
                        TEL：{{ $invoice->company->tel }}
                    @endif

                    @if(!empty($invoice->company->fax))
                        &nbsp;&nbsp;
                        FAX：{{ $invoice->company->fax }}
                    @endif

                    @if(!empty($invoice->company->email))
                        <br>
                        {{ $invoice->company->email }}
                    @endif

                </div>

                @if(!empty($invoice->company->registration_number))
                    <div class="registration-number">
                        登録番号：{{ $invoice->company->registration_number }}
                    </div>
                @endif

            </td>

        </tr>
    </table>


    {{-- =========================
         請求日・支払期限
    ========================= --}}
    <table class="invoice-info-table">

        <tr>

            <th>
                請求日
            </th>

            <td>
                {{ $invoice->invoice_date?->format('Y年m月d日') }}
            </td>

            <th>
                支払期限
            </th>

            <td>
                {{ $invoice->payment_due?->format('Y年m月d日') }}
            </td>

        </tr>

    </table>


    {{-- =========================
         請求金額
    ========================= --}}
    <div class="total-box">

        <table style="width:100%; border-collapse:collapse;">
            <tr>

                <td class="total-label">
                    ご請求金額
                </td>

                <td class="total-price">
                    ¥{{ number_format($invoice->total) }}
                </td>

            </tr>
        </table>

    </div>


    {{-- =========================
         明細
    ========================= --}}
    <div class="section-title">
        明細
    </div>

    <table class="details">

        <thead>

            <tr>

                <th class="site-col">
                    現場名
                </th>

                <th class="name-col">
                    品名
                </th>

                <th class="quantity-col">
                    数量
                </th>

                <th class="unit-col">
                    単位
                </th>

                <th class="tax-col">
                    税率
                </th>

                <th class="unit-price-col">
                    単価
                </th>

                <th class="amount-col">
                    金額
                </th>

            </tr>

        </thead>

        <tbody>

            @foreach($invoice->details as $detail)

                <tr>

                    {{-- 現場名 --}}
                    <td>
                        {{ $detail->site->name ?? '-' }}
                    </td>

                    {{-- 品名 --}}
                    <td>
                        {{ $detail->description }}
                    </td>

                    {{-- 数量 --}}
                    <td class="center">
                        {{ rtrim(rtrim(number_format($detail->quantity, 2), '0'), '.') }}
                    </td>

                    {{-- 単位 --}}
                    <td class="center">
                        {{ $detail->unit ?? '-' }}
                    </td>

                    {{-- 税率 --}}
                    <td class="center nowrap">

                        @if($detail->tax_type === 'taxable')
                            10%
                        @else
                            非課税
                        @endif

                    </td>

                    {{-- 単価 --}}
                    <td class="right">
                        ¥{{ number_format($detail->unit_price) }}
                    </td>

                    {{-- 金額 --}}
                    <td class="right">
                        ¥{{ number_format($detail->amount) }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>


    {{-- =========================
         集計
    ========================= --}}
    <div class="summary-area">

        <table>

            <tr>

                <th>
                    小計
                </th>

                <td>
                    ¥{{ number_format($invoice->subtotal) }}
                </td>

            </tr>

            <tr>

                <th>
                    消費税
                </th>

                <td>
                    ¥{{ number_format($invoice->tax) }}
                </td>

            </tr>

            <tr class="summary-total">

                <th>
                    合計
                </th>

                <td>
                    ¥{{ number_format($invoice->total) }}
                </td>

            </tr>

        </table>

    </div>


    {{-- =========================
         振込先・備考
    ========================= --}}
    <table class="bottom-table">

        <tr>

            {{-- 振込先 --}}
            <td class="bank-area">

                <div class="section-title">
                    お振込先
                </div>

                <table class="bank-table">

                    @if(!empty($invoice->company->bank_name))
                        <tr>
                            <th>銀行名</th>
                            <td>{{ $invoice->company->bank_name }}</td>
                        </tr>
                    @endif

                    @if(!empty($invoice->company->bank_branch_name))
                        <tr>
                            <th>支店名</th>
                            <td>{{ $invoice->company->bank_branch_name }}</td>
                        </tr>
                    @endif

                    @if(!empty($invoice->company->bank_account_type))
                        <tr>
                            <th>口座種別</th>
                            <td>{{ $invoice->company->bank_account_type }}</td>
                        </tr>
                    @endif

                    @if(!empty($invoice->company->bank_account_number))
                        <tr>
                            <th>口座番号</th>
                            <td>{{ $invoice->company->bank_account_number }}</td>
                        </tr>
                    @endif

                    @if(!empty($invoice->company->bank_account_name))
                        <tr>
                            <th>口座名義</th>
                            <td>{{ $invoice->company->bank_account_name }}</td>
                        </tr>
                    @endif

                </table>

            </td>


            {{-- 備考 --}}
            <td class="remarks-area">

                <div class="section-title">
                    備考
                </div>

                <div class="remarks-box">

                    @if(!empty($invoice->remarks))
                        {!! nl2br(e($invoice->remarks)) !!}
                    @endif

                </div>

            </td>

        </tr>

    </table>

</div>

@endsection

