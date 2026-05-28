<x-app-layout>

    <div class="max-w-5xl mx-auto py-10">

        <div class="flex justify-between items-center mb-5">

            <h1 class="text-2xl font-bold">
                下請け一覧
            </h1>

            <a href="{{ route('subcontractors.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded shadow">

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

                    <th class="border p-2">
                        ID
                    </th>

                    <th class="border p-2">
                        名前
                    </th>

                    <th class="border p-2">
                        操作
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($subcontractors as $subcontractor)

                <tr>

                    <td class="border p-2">

                        {{ $subcontractor->id }}

                    </td>

                    <td class="border p-2">

                        {{ $subcontractor->name }}

                    </td>

                    <td class="border p-2">

                        <div class="flex gap-2">

                            <a href="{{ route('subcontractors.edit', $subcontractor) }}"
                                class="bg-yellow-400 px-3 py-1 rounded">

                                編集

                            </a>

                            <form action="{{ route('subcontractors.destroy', $subcontractor) }}"
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