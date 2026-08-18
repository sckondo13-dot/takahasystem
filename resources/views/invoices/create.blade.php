<x-app-layout>

    <div class="max-w-6xl mx-auto py-8">

        <h1 class="text-2xl font-bold mb-6">

            請求書作成

        </h1>

        <form
            method="POST"
            action="{{ route('invoices.store') }}">

            @csrf

            <div class="grid grid-cols-4 gap-5">

                {{-- 元請 --}}
                <div>

                    <label class="block mb-1">

                        元請

                    </label>

                    <select
                        id="client_id"
                        name="client_id"
                        class="border rounded w-full">

                        <option value="">

                            選択してください

                        </option>

                        @foreach($clients as $client)

                        <option
                            value="{{ $client->id }}">

                            {{ $client->name }}

                        </option>

                        @endforeach

                    </select>

                </div>

                {{-- 請求月 --}}
                <div>

                    <label class="block mb-1">

                        請求月

                    </label>

                    <input
                        type="month"
                        id="month"
                        name="month"
                        class="border rounded w-full">

                </div>

                {{-- 現場 --}}
                <div>

                    <label class="block mb-1">

                        現場

                    </label>

                    <select
                        id="site_id"
                        name="site_id"
                        class="border rounded w-full">

                        <option>

                            元請・月を選択してください

                        </option>

                    </select>

                </div>

            </div>

            <hr class="my-8">

            <div class="grid grid-cols-2 gap-6">

                <div>

                    <label class="block mb-1">
                        請求日
                    </label>

                    <input
                        type="date"
                        name="invoice_date"
                        value="{{ now()->format('Y-m-d') }}"
                        class="border rounded w-full">

                </div>

                <div>

                    <label class="block mb-1">
                        支払期限
                    </label>

                    <input
                        type="date"
                        name="payment_due"
                        value="{{ now()->addMonth()->endOfMonth()->format('Y-m-d') }}"
                        class="border rounded w-full">

                </div>

                <div>

                    <label class="block mb-1">
                        件名
                    </label>

                    <input
                        type="text"
                        name="title"
                        placeholder="○月分 解体工事"
                        class="border rounded w-full">

                </div>

                <div>

                    <label class="block mb-1">
                        請求書番号
                    </label>

                    <input
                        type="text"
                        name="invoice_no"
                        readonly
                        value="{{ $invoiceNo ?? '' }}"
                        class="border rounded w-full bg-gray-100">

                </div>

            </div>

            <hr class="my-8">

            {{-- 自動集計 --}}
            <div
                id="summaryArea"
                class="hidden">
                <input
                    type="hidden"
                    id="sales_input"
                    name="sales">

                <input
                    type="hidden"
                    id="transportation_input"
                    name="transportation">

                <input
                    type="hidden"
                    id="man_hours_input"
                    name="man_hours">

                <input
                    type="hidden"
                    id="unit_price_input"
                    name="unit_price">

                <table class="w-full border">

                    <tr>

                        <th class="border p-2">

                            人工

                        </th>

                        <td
                            id="man_hours"
                            class="border p-2">

                        </td>

                    </tr>

                    <tr>

                        <th class="border p-2">

                            解体単価

                        </th>

                        <td
                            id="unit_price"
                            class="border p-2">

                        </td>

                    </tr>

                    <tr>

                        <th class="border p-2">

                            売上

                        </th>

                        <td
                            id="sales"
                            class="border p-2">

                        </td>

                    </tr>

                    <tr>

                        <th class="border p-2">

                            交通費

                        </th>

                        <td
                            id="transportation"
                            class="border p-2">

                        </td>

                    </tr>

                </table>

            </div>

            <div class="mt-8">

                <button
                    class="bg-green-600 text-white px-6 py-2 rounded">

                    請求書作成

                </button>

            </div>

        </form>

    </div>

</x-app-layout>