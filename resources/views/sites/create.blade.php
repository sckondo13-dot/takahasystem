<x-app-layout>

    <div class="max-w-3xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-5">
            現場登録
        </h1>

        <form
            action="{{ route('sites.store') }}"
            method="POST"
            class="space-y-5">

            @csrf

            {{-- 元請け --}}
            <div>

                <label class="block mb-1">
                    元請け
                </label>

                <select
                    name="client_id"
                    class="w-full border rounded p-2">

                    <option value="">
                        選択してください
                    </option>

                    @foreach($clients as $client)

                        <option
                            value="{{ $client->id }}"
                            {{ old('client_id') == $client->id ? 'selected' : '' }}>

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

                <input
                    type="text"
                    name="name"
                    class="w-full border rounded p-2"
                    value="{{ old('name') }}">

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

                <select
                    name="contract_type"
                    class="w-full border rounded p-2">

                    <option
                        value="請負"
                        {{ old('contract_type', '請負') == '請負' ? 'selected' : '' }}>

                        請負

                    </option>

                    <option
                        value="常用"
                        {{ old('contract_type') == '常用' ? 'selected' : '' }}>

                        常用

                    </option>

                </select>

                @error('contract_type')

                    <div class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            {{-- 請負契約金額 --}}
            <div>

                <label
                    for="contract_amount"
                    class="block mb-1">

                    請負契約金額

                </label>

                <input
                    type="number"
                    id="contract_amount"
                    name="contract_amount"
                    value="{{ old('contract_amount') }}"
                    min="0"
                    step="1"
                    class="w-full border rounded p-2">

                @error('contract_amount')

                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- 残率 --}}
            <div>

                <label
                    for="remaining_rate"
                    class="block mb-1">

                    残率（％）

                </label>

                <input
                    type="number"
                    id="remaining_rate"
                    name="remaining_rate"
                    value="{{ old('remaining_rate') }}"
                    min="0"
                    max="100"
                    step="1"
                    class="w-full border rounded p-2">

                @error('remaining_rate')

                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- 開始月 --}}
            <div>

                <label
                    for="contract_start"
                    class="block mb-1">

                    開始月

                </label>

                <input
                    type="month"
                    id="contract_start"
                    name="contract_start"
                    value="{{ old('contract_start') }}"
                    class="border rounded p-2">

                @error('contract_start')

                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- 終了月 --}}
            <div>

                <label
                    for="contract_end"
                    class="block mb-1">

                    終了月

                </label>

                <input
                    type="month"
                    id="contract_end"
                    name="contract_end"
                    value="{{ old('contract_end') }}"
                    class="border rounded p-2">

                @error('contract_end')

                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- 登録ボタン --}}
            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">

                登録

            </button>

        </form>

    </div>

</x-app-layout>
