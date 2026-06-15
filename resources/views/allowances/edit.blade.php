<x-app-layout>

    <div class="max-w-3xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-5">
            手当編集
        </h1>

        <form action="{{ route('allowances.update', $allowance) }}"
            method="POST">

            @csrf
            @method('PUT')

            {{-- 名称 --}}
            <div class="mb-4">

                <label class="block mb-1">
                    名称
                </label>

                <input type="text"
                    name="name"
                    class="w-full border rounded p-2"
                    value="{{ old('name', $allowance->name) }}">

                @error('name')
                <div class="text-red-500 text-sm">
                    {{ $message }}
                </div>
                @enderror

            </div>

            {{-- 種別 --}}
            <div class="mb-4">

                <label class="block mb-1">
                    種別
                </label>

                <select name="type"
                    class="w-full border rounded p-2">

                    <option value="fixed"
                        {{ old('type', $allowance->type) == 'fixed' ? 'selected' : '' }}>

                        固定手当

                    </option>

                    <option value="work"
                        {{ old('type', $allowance->type) == 'work' ? 'selected' : '' }}>

                        作業手当

                    </option>

                </select>

            </div>

            {{-- 金額 --}}
            <div class="mb-4">

                <label class="block mb-1">
                    金額
                </label>

                <input type="number"
                    name="amount"
                    class="w-full border rounded p-2"
                    value="{{ old('amount', $allowance->amount) }}">

            </div>

            {{-- 備考 --}}
            <div class="mb-4">

                <label class="block mb-1">
                    備考
                </label>

                <textarea name="note"
                    rows="3"
                    class="w-full border rounded p-2">{{ old('note', $allowance->note) }}</textarea>

            </div>

            <div class="flex gap-3">

                <button
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">

                    更新

                </button>

                <a href="{{ route('allowances.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded">

                    戻る

                </a>

            </div>

        </form>

    </div>

</x-app-layout>