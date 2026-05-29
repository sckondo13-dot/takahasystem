<x-app-layout>

    <div class="max-w-3xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-5">
            現場登録
        </h1>

        <form action="{{ route('sites.store') }}"
            method="POST"
            class="space-y-5">

            @csrf

            <div>

                <label class="block mb-1">
                    元請け
                </label>

                <select name="client_id"
                    class="w-full border rounded p-2">

                    <option value="">
                        選択してください
                    </option>

                    @foreach($clients as $client)

                    <option value="{{ $client->id }}">

                        {{ $client->name }}

                    </option>

                    @endforeach

                </select>

            </div>

            <div>

                <label class="block mb-1">
                    現場名
                </label>

                <input type="text"
                    name="name"
                    class="w-full border rounded p-2">

            </div>

            <div>

                <label class="block mb-1">
                    契約種別
                </label>

                <select name="contract_type"
                    class="w-full border rounded p-2">

                    <option value="請負">
                        請負
                    </option>

                    <option value="常用">
                        常用
                    </option>

                </select>

            </div>

            <button class="bg-blue-600 text-white px-5 py-2 rounded">

                登録

            </button>

        </form>

    </div>

</x-app-layout>