<x-app-layout>

    <div class="max-w-5xl mx-auto py-8">

        {{-- ヘッダー --}}
        <div class="flex justify-between items-center mb-6">

            <h1 class="text-2xl font-bold">
                請求書
            </h1>

            <div class="flex gap-2">

                <a
                    href="{{ route('invoices.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">

                    一覧へ戻る

                </a>

            </div>

        </div>

        @if(session('success'))

        <div class="bg-green-100 text-green-700 p-3 rounded mb-5">

            {{ session('success') }}

        </div>

        @endif

        {{-- 請求情報 --}}
        <div class="border rounded p-5 mb-6">

            <h2 class="text-lg font-bold mb-4">
                請求情報
            </h2>

            <div class="grid grid-cols-2 gap-4">

                <div>

                    <div class="text-gray-500 text-sm">
                        請求書番号
                    </div>

                    <div class="font-bold">
                        {{ $invoice->invoice_no }}
                    </div>

                </div>

                <div>

                    <div class="text-gray-500 text-sm">
                        取引先
                    </div>

                    <div class="font-bold">
                        {{ $invoice->client->name }}
                    </div>

                </div>

                <div>

                    <div class="text-gray-500 text-sm">
                        請求日
                    </div>

                    <div>
                        {{ optional($invoice->invoice_date)->format('Y年m月d日') }}
                    </div>

                </div>

                <div>

                    <div class="text-gray-500 text-sm">
                        お支払い期限
                    </div>

                    <div>
                        {{ optional($invoice->payment_due)->format('Y年m月d日') }}
                    </div>

                </div>

            </div>

        </div>

        {{-- 請求明細 --}}
        <div class="border rounded p-5 mb-6">

            <h2 class="text-lg font-bold mb-4">
                請求明細
            </h2>

            <div class="overflow-x-auto">

                <table class="w-full border-collapse">

                    <thead>

                        <tr class="bg-gray-100">

                            <th class="border p-3 text-left">
                                No.
                            </th>

                            <th class="border p-3 text-left">
                                請求内容
                            </th>

                            <th class="border p-3 text-right">
                                数量
                            </th>

                            <th class="border p-3 text-left">
                                単位
                            </th>

                            <th class="border p-3 text-left">
                                税区分
                            </th>

                            <th class="border p-3 text-right">
                                単価
                            </th>

                            <th class="border p-3 text-right">
                                金額
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($invoice->details as $detail)

                        <tr>

                            <td class="border p-3 text-center">
                                {{ $detail->sort_order }}
                            </td>

                            <td class="border p-3">
                                {{ $detail->description }}
                            </td>

                            <td class="border p-3 text-right">
                                {{ number_format($detail->quantity, 2) }}
                            </td>

                            <td class="border p-3">
                                {{ $detail->unit }}
                            </td>

                            <td class="border p-3">
                                {{ $detail->tax_type === 'taxable' ? '課税' : '非課税' }}
                            </td>

                            <td class="border p-3 text-right">
                                {{ number_format($detail->unit_price) }} 円
                            </td>

                            <td class="border p-3 text-right">
                                {{ number_format($detail->amount) }} 円
                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td
                                colspan="7"
                                class="border p-3 text-center text-gray-500">

                                明細がありません。

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- 金額 --}}
        <div class="border rounded p-5 mb-6">

            <h2 class="text-lg font-bold mb-4">
                請求金額
            </h2>

            <table class="w-full border-collapse">

                <tbody>

                    <tr>

                        <th class="border p-3 text-left bg-gray-50">
                            小計
                        </th>

                        <td class="border p-3 text-right">
                            {{ number_format($invoice->subtotal) }} 円
                        </td>

                    </tr>

                    <tr>

                        <th class="border p-3 text-left bg-gray-50">
                            消費税
                        </th>

                        <td class="border p-3 text-right">
                            {{ number_format($invoice->tax) }} 円
                        </td>

                    </tr>

                    <tr class="font-bold text-lg">

                        <th class="border p-3 text-left bg-gray-100">
                            合計
                        </th>

                        <td class="border p-3 text-right bg-gray-100">
                            {{ number_format($invoice->total) }} 円
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


        {{-- 備考 --}}
        @if($invoice->remarks)

        <div class="border rounded p-5 mb-6">

            <h2 class="text-lg font-bold mb-3">
                備考
            </h2>

            <div class="whitespace-pre-line">
                {{ $invoice->remarks }}
            </div>

        </div>

        @endif


        {{-- 操作 --}}
        <div class="flex gap-3">

            <a
                href="{{ route('invoices.edit', $invoice) }}"
                class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded">

                編集

            </a>

            <a
                href="{{ route('invoices.pdf', $invoice) }}"
                target="_blank"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded">

                PDF表示

            </a>

            <a
                href="{{ route('invoices.pdf.download', $invoice) }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">

                PDFダウンロード

            </a>

            <a
                href="{{ route('invoices.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded">

                一覧へ戻る

            </a>

        </div>

    </div>

</x-app-layout>