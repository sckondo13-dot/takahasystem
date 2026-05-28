<x-app-layout>

    <div class="max-w-7xl mx-auto py-10">

        <div class="flex justify-between items-center mb-5">

            <h1 class="text-2xl font-bold">
                元請け一覧
            </h1>

            <a href="{{ route('clients.create') }}"
                class="bg-blue-600 text-white px-5 py-2 rounded">

                ＋ 新規登録

            </a>

        </div>

        @if(session('success'))

        <div class="bg-green-100 text-green-700 p-3 mb-5 rounded">

            {{ session('success') }}

        </div>

        @endif

        <table class="w-full border">

            <thead class="bg-gray-100">

                <tr>

                    <th class="border p-2">名前</th>
                    <th class="border p-2">解体</th>
                    <th class="border p-2">重機</th>
                    <th class="border p-2">重機2</th>
                    <th class="border p-2">はつり</th>
                    <th class="border p-2">石綿</th>
                    <th class="border p-2">トラック</th>
                    <th class="border p-2">ユニック</th>
                    <th class="border p-2">操作</th>

                </tr>

            </thead>

            <tbody>

                @foreach($clients as $client)

                <tr>

                    <td class="border p-2">
                        {{ $client->name }}
                    </td>

                    <td class="border p-2">
                        {{ number_format($client->demolition_unit_price) }}
                    </td>

                    <td class="border p-2">
                        {{ number_format($client->heavy_equipment_unit_price) }}
                    </td>

                    <td class="border p-2">
                        {{ number_format($client->heavy_equipment2_unit_price) }}
                    </td>

                    <td class="border p-2">
                        {{ number_format($client->chipping_unit_price) }}
                    </td>

                    <td class="border p-2">
                        {{ number_format($client->asbestos_unit_price) }}
                    </td>

                    <td class="border p-2">
                        {{ number_format($client->truck_unit_price) }}
                    </td>

                    <td class="border p-2">
                        {{ number_format($client->unic_unit_price) }}
                    </td>

                    <td class="border p-2">

                        <div class="flex gap-2">

                            <a href="{{ route('clients.edit', $client) }}"
                                class="bg-yellow-400 px-3 py-1 rounded">

                                編集

                            </a>

                            <form action="{{ route('clients.destroy', $client) }}"
                                method="POST">

                                @csrf
                                @method('DELETE')

                                <button
                                    class="bg-red-500 text-white px-3 py-1 rounded"
                                    onclick="return confirm('削除しますか？')">

                                    削除

                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</x-app-layout>