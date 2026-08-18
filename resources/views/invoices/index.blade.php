<x-app-layout>

    <div class="max-w-7xl mx-auto py-8">

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

        <form method="GET" class="mb-5 flex gap-3 flex-wrap">

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

                <option value="draft">

                    下書き

                </option>

                <option value="issued">

                    発行済

                </option>

                <option value="paid">

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
                class="bg-blue-600 text-white px-4 rounded">

                検索

            </button>

        </form>

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

                        {{ optional($invoice->site)->name }}

                    </td>

                    <td class="border p-2">

                        {{ $invoice->invoice_date->format('Y/m/d') }}

                    </td>

                    <td class="border p-2 text-right">

                        {{ number_format($invoice->total) }}

                    </td>

                    <td class="border p-2">

                        {{ $invoice->status }}

                    </td>

                    <td class="border p-2">

                        <a
                            href="{{ route('invoices.edit',$invoice) }}"
                            class="text-blue-600">

                            編集

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

        {{ $invoices->links() }}

    </div>

</x-app-layout>