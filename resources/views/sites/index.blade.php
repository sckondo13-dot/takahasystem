<x-app-layout>

    <div class="max-w-6xl mx-auto py-10">

        <div class="flex justify-between items-center mb-5">

            <h1 class="text-2xl font-bold">
                現場一覧
            </h1>

            <a href="{{ route('sites.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded shadow">

                ＋ 新規登録

            </a>

        </div>

        <form method="GET" class="mb-5">

            <div class="flex flex-wrap gap-3 items-end">

                <div>
                    <label class="block text-sm mb-1">
                        現場名
                    </label>

                    <input
                        type="text"
                        name="keyword"
                        value="{{ request('keyword') }}"
                        placeholder="現場名検索"
                        class="border rounded p-2">
                </div>

                <div>
                    <label class="block text-sm mb-1">
                        元請
                    </label>

                    <select
                        name="client_id"
                        class="border rounded p-2">

                        <option value="">
                            全て
                        </option>

                        @foreach($clients as $client)

                        <option
                            value="{{ $client->id }}"
                            @selected(request('client_id')==$client->id)>

                            {{ $client->name }}

                        </option>

                        @endforeach

                    </select>

                </div>

                <div>
                    <label class="block text-sm mb-1">
                        状況
                    </label>

                    <select
                        name="status"
                        class="border rounded p-2">

                        <option value="">
                            全て
                        </option>

                        <option value="active"
                            @selected(request('status')=='active' )>

                            解体中

                        </option>

                        <option value="future"
                            @selected(request('status')=='future' )>

                            未開始

                        </option>

                        <option value="finished"
                            @selected(request('status')=='finished' )>

                            終了

                        </option>

                    </select>

                </div>

                <button
                    class="bg-blue-600 text-white px-4 py-2 rounded">

                    検索

                </button>

                <a
                    href="{{ route('sites.index') }}"
                    class="bg-gray-400 text-white px-4 py-2 rounded">

                    リセット

                </a>

            </div>

        </form>

        @if(session('success'))

        <div class="bg-green-100 text-green-700 p-3 mb-5 rounded">

            {{ session('success') }}

        </div>

        @endif

        <table class="w-full border">

            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">
                        現場名
                    </th>

                    <th class="border p-2">
                        元請
                    </th>

                    <th class="border p-2">
                        契約区分
                    </th>

                    <th class="border p-2">
                        契約期間
                    </th>

                    <th class="border p-2">
                        状況
                    </th>

                    <th class="border p-2">
                        操作
                    </th>
                </tr>
            </thead>

            <tbody>

                @foreach($sites as $site)

                <tr>

                    <td class="border p-2">
                        {{ $site->name }}
                    </td>

                    <td class="border p-2">
                        {{ $site->client->name }}
                    </td>

                    <td class="border p-2 text-center">
                        {{ $site->contract_type }}
                    </td>

                    <td class="border p-2 text-center">

                        {{ optional($site->contract_start)->format('Y/m') }}
                        ～

                        {{ $site->contract_end
        ? $site->contract_end->format('Y/m')
        : '未定' }}

                    </td>

                    <td class="border p-2 text-center">

                        <span class="{{ $site->status_color }} px-2 py-1 rounded">

                            {{ $site->status }}

                        </span>

                    </td>

                    <td class="border p-2">

                        <div class="flex gap-2">

                            <a
                                href="{{ route('sites.edit', $site) }}"
                                class="bg-yellow-400 hover:bg-yellow-500 px-3 py-1 rounded">

                                編集

                            </a>

                            <form
                                action="{{ route('sites.destroy', $site) }}"
                                method="POST">

                                @csrf
                                @method('DELETE')

                                <button
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
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