<x-app-layout>

    <div class="max-w-3xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-5">
            現場編集
        </h1>

        <form action="{{ route('sites.update', $site) }}"
            method="POST"
            class="space-y-5">

            @csrf
            @method('PUT')

            {{-- 元請け --}}
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

                    <option value="{{ $client->id }}"
                        {{ old('client_id', $site->client_id) == $client->id ? 'selected' : '' }}>

                        {{ $client->name }}

                    </option>

                    @endforeach

                </select>

                @error('client_id')

                <div class="text-red-500 text-sm mt-1">

                    {{ $message }}

                </div>

                @enderror

            </div>

            {{-- 現場名 --}}
            <div>

                <label class="block mb-1">
                    現場名
                </label>

                <input type="text"
                    name="name"
                    class="w-full border rounded p-2"
                    value="{{ old('name', $site->name) }}">

                @error('name')

                <div class="text-red-500 text-sm mt-1">

                    {{ $message }}

                </div>

                @enderror

            </div>

            {{-- 契約種別 --}}
            <div>

                <label class="block mb-1">
                    契約種別
                </label>

                <select name="contract_type"
                    class="w-full border rounded p-2">

                    <option value="請負"
                        {{ old('contract_type', $site->contract_type) == '請負' ? 'selected' : '' }}>

                        請負

                    </option>

                    <option value="常用"
                        {{ old('contract_type', $site->contract_type) == '常用' ? 'selected' : '' }}>

                        常用

                    </option>

                </select>

                @error('contract_type')

                <div class="text-red-500 text-sm mt-1">

                    {{ $message }}

                </div>

                @enderror

            </div>

            <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">

                更新

            </button>

        </form>

    </div>

</x-app-layout>