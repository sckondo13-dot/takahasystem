<x-app-layout>

    <div class="max-w-7xl mx-auto py-8">

        {{-- ヘッダー --}}
        <div class="flex justify-between mb-5">

            <h1 class="text-2xl font-bold">
                請求書一覧
            </h1>

            <a
                href="{{ route('invoices.create') }}"
                class="bg-blue-600 text-white px-5 py-2 rounded">

                ＋請求書作成

            </a>

        </div>

        {{-- 検索 --}}
        <form
            method="GET"
            class="mb-5 flex gap-3 flex-wrap">

            <input
                type="month"
                name="month"
                value="{{ request('month') }}"
                class="border rounded p-2">

            <select
                name="client_id"
                class="border rounded p-2">

                <option value="">
                    元請
                </option>

                @foreach($clients as $client)

                <option
                    value="{{ $client->id }}"
                    @selected(request('client_id')==$client->id)>

                    {{ $client->name }}

                </option>

                @endforeach

            </select>

            <select
                name="status"
                class="border rounded p-2">

                <option value="">
                    状態
                </option>

                <option
                    value="draft"
                    @selected(request('status')==='draft' )>

                    下書き

                </option>

                <option
                    value="issued"
                    @selected(request('status')==='issued' )>

                    発行済

                </option>

                <option
                    value="paid"
                    @selected(request('status')==='paid' )>

                    入金済

                </option>

            </select>

            <input
                type="text"
                name="keyword"
                value="{{ request('keyword') }}"
                placeholder="請求番号"
                class="border rounded p-2">

            <button
                type="submit"
                class="bg-blue-600 text-white px-4 rounded">

                検索

            </button>

        </form>

        {{-- 請求書一覧 --}}
        <div class="overflow-x-auto">

            <table class="w-full border">

                <thead class="bg-gray-100">

                    <tr>

                        <th class="border p-2">
                            請求番号
                        </th>

                        <th class="border p-2">
                            請求先
                        </th>

                        <th class="border p-2">
                            現場
                        </th>

                        <th class="border p-2">
                            請求日
                        </th>

                        <th class="border p-2">
                            金額
                        </th>

                        <th class="border p-2">
                            状態
                        </th>

                        <th class="border p-2">
                            操作
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($invoices as $invoice)

                    <tr>

                        <td class="border p-2">
                            {{ $invoice->invoice_no }}
                        </td>

                        <td class="border p-2">
                            {{ $invoice->client->name }}
                        </td>

                        <td class="border p-2">

                            @php
                            $siteNames = $invoice->details
                            ->pluck('site.name')
                            ->filter()
                            ->unique()
                            ->values();
                            @endphp

                            {{ $siteNames->isNotEmpty()
                                    ? $siteNames->implode('、')
                                    : '現場指定なし' }}

                        </td>

                        <td class="border p-2">
                            {{ optional($invoice->invoice_date)->format('Y/m/d') }}
                        </td>

                        <td class="border p-2 text-right">
                            {{ number_format($invoice->total) }} 円
                        </td>

                        <td class="border p-2">

                            @switch($invoice->status)

                            @case('draft')
                            下書き
                            @break

                            @case('issued')
                            発行済
                            @break

                            @case('paid')
                            入金済
                            @break

                            @default
                            {{ $invoice->status }}

                            @endswitch

                        </td>

                        <td class="border p-2">

                            <a
                                href="{{ route('invoices.show', $invoice) }}"
                                class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">

                                詳細

                            </a>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="7"
                            class="text-center p-5">

                            データがありません

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- ページネーション --}}
        <div class="mt-5">

            {{ $invoices->links() }}

        </div>

    </div>

</x-app-layout>