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
                        現場
                    </div>

                    <div>
                        {{ $invoice->site?->name ?? '現場指定なし' }}
                    </div>

                </div>

                <div>

                    <div class="text-gray-500 text-sm">
                        請求月
                    </div>

                    <div>
                        {{ $invoice->title }}
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

        </div>

    </div>

</x-app-layout>