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

    /* タイトル */
    .invoice-title {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 24px;
        letter-spacing: 6px;
    }

    /* 宛先・発行者 */
    .header-table {
        width: 100%;
        border-collapse: collapse;
        border: none;
        margin-bottom: 18px;
    }

    .header-table td {
        border: none;
        padding: 0;
    }

    .client-area {
        width: 55%;
        vertical-align: top;
        padding-top: 8px !important;
    }

    .company-area {
        width: 45%;
        vertical-align: top;
        text-align: right;
    }

    .client-label {
        font-size: 10px;
        margin-bottom: 8px;
        color: #555;
    }

    .client-name {
        display: inline-block;
        font-size: 16px;
        font-weight: bold;
        border-bottom: 1px solid #222;
        padding-bottom: 6px;
        min-width: 200px;
    }

    .company-name {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 4px;
    }

    .company-info {
        line-height: 1.3;
        font-size: 9px;
    }

    .registration-number {
        margin-top: 2px;
        font-size: 9px;
        font-weight: bold;
    }

    /* 請求日 */
    .invoice-date {
        margin-top: 8px;
        font-size: 10px;
    }

    /* ご請求金額・支払期限 */
    .billing-row {
        width: 100%;
        border-collapse: collapse;
        margin: 12px 0 20px;
    }

    .billing-row td {
        border: none;
        border-bottom: 1px solid #555;
        padding: 8px 5px;
        vertical-align: bottom;
    }

    .billing-label {
        font-size: 10px;
        color: #555;
        white-space: nowrap;
    }

    .billing-total {
        width: 55%;
    }

    .billing-total-price {
        font-size: 20px;
        font-weight: bold;
        padding-left: 12px !important;
        white-space: nowrap;
    }

    .billing-due {
        width: 45%;
        text-align: right;
        white-space: nowrap;
    }

    .billing-due-date {
        font-size: 13px;
        font-weight: bold;
        padding-left: 8px !important;
    }

    /* 明細テーブル */
    .details {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .details th,
    .details td {
        border: 1px solid #aaa;
        padding: 4px 4px;
    }

    .details th {
        background: #f1f1f1;
        text-align: center;
        font-weight: bold;
        font-size: 9px;
        padding-top: 5px;
        padding-bottom: 5px;
    }

    .details td {
        vertical-align: middle;
        line-height: 1.3;
    }

    /* 列幅：合計100% */
    .details .site-col {
        width: 23%;
    }

    .details .name-col {
        width: 31%;
    }

    .details .quantity-col {
        width: 6%;
    }

    .details .unit-col {
        width: 6%;
    }

    .details .tax-col {
        width: 8%;
    }

    .details .unit-price-col {
        width: 13%;
    }

    .details .amount-col {
        width: 13%;
    }

    .details .center {
        text-align: center;
    }

    .details .right {
        text-align: right;
    }

    .nowrap {
        white-space: nowrap;
    }

    /* 集計 */
    .summary-area {
        width: 42%;
        margin-left: auto;
        margin-top: 12px;
    }

    .summary-area table {
        width: 100%;
        border-collapse: collapse;
    }

    .summary-area th,
    .summary-area td {
        border-bottom: 1px solid #ccc;
        padding: 5px 7px;
    }

    .summary-area th {
        width: 45%;
        background: #f7f7f7;
        text-align: center;
        font-weight: normal;
    }

    .summary-area td {
        text-align: right;
    }

    .summary-total th,
    .summary-total td {
        font-size: 12px;
        font-weight: bold;
        border-top: 1px solid #555;
        border-bottom: 2px solid #555;
    }

    /* 振込先・備考 */
    .bottom-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 22px;
    }

    .bottom-table>tbody>tr>td {
        border: none;
        padding: 0 10px 0 0;
        vertical-align: top;
    }

    .bank-area {
        width: 50%;
    }

    .remarks-area {
        width: 50%;
    }

    .bank-table {
        width: 100%;
        border-collapse: collapse;
    }

    .bank-table th,
    .bank-table td {
        border: none;
        padding: 2px 4px 2px 0;
        text-align: left;
        vertical-align: top;
        line-height: 1.4;
    }

    .bank-table th {
        width: 30%;
        font-weight: normal;
        color: #555;
    }

    .section-title {
        font-size: 10px;
        font-weight: bold;
        border-bottom: 1px solid #aaa;
        padding-bottom: 4px;
        margin-bottom: 6px;
    }

    .remarks-box {
        border: none;
        padding: 2px 0;
        line-height: 1.5;
        min-height: 60px;
    }

    @media print {
        body {
            margin: 0;
        }
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

                <div class="invoice-date">
                    請求日：
                    {{ $invoice->invoice_date?->format('Y年m月d日') }}
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
         請求金額
    ========================= --}}
    <div>下記の通りご請求書申し上げます。</div>
    <table class="billing-row">
        <tr>
            <td class="billing-total">
                <span class="billing-label">ご請求金額</span>
                <span class="billing-total-price">
                    ¥{{ number_format($invoice->total) }}
                </span>
            </td>

            <td class="billing-due">
                <span class="billing-label">お支払期限</span>
                <span class="billing-due-date">
                    {{ $invoice->payment_due?->format('Y年m月d日') }}
                </span>
            </td>
        </tr>
    </table>


    {{-- =========================
         明細
    ========================= --}}

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