<x-app-layout>

    <div class="max-w-6xl mx-auto py-10">

        <div class="flex justify-between mb-5">

            <h1 class="text-2xl font-bold">

                手当一覧

            </h1>

            <a href="{{ route('allowances.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded">

                新規登録

            </a>

        </div>

        <table class="w-full border">

            <thead class="bg-gray-100">

                <tr>

                    <th class="border p-2">
                        名称
                    </th>

                    <th class="border p-2">
                        種別
                    </th>

                    <th class="border p-2">
                        金額
                    </th>
                    <th class="border p-2">
                        
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($allowances as $allowance)

                <tr>

                    <td class="border p-2">
                        {{ $allowance->name }}
                    </td>

                    <td class="border p-2">

                        {{ $allowance->type === 'fixed'
                            ? '固定手当'
                            : '作業手当' }}

                    </td>

                    <td class="border p-2 text-right">

                        {{ number_format($allowance->amount) }}

                    </td>
                    <td class="border p-2 text-right">

                        <a href="{{ route('allowances.edit', $allowance) }}"
                            class="bg-yellow-500 text-white px-3 py-1 rounded">

                            編集

                        </a>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</x-app-layout>