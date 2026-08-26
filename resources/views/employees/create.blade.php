<x-app-layout>

    <div class="max-w-3xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-5">
            従業員登録
        </h1>

        <form action="{{ route('employees.store') }}"
            method="POST"
            class="space-y-5">

            @csrf

            <div>
                <div class="mb-3">
                    <label class="block mb-1">
                        名前
                    </label>

                    <input type="text"
                        name="name"
                        class="w-full border rounded p-2"
                        value="{{ old('name') }}">

                    @error('name')
                    <div class="text-red-500 text-sm">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="mb-3">

                    <label class="block mb-1">
                        雇用日
                    </label>

                    <input
                        type="date"
                        name="hire_date"
                        class="w-full border rounded p-2"
                        value="{{ old('hire_date') }}"
                        required>

                    @error('hire_date')
                    <div class="text-red-500 text-sm">
                        {{ $message }}
                    </div>
                    @enderror

                </div>
                <div>

                    <h3 class="font-bold mb-3">

                        手当設定

                    </h3>

                    {{-- 固定手当 --}}
                    <div class="mb-5">

                        <div class="font-bold text-blue-600 mb-2">

                            固定手当

                        </div>

                        @foreach($allowances->where('type', 'fixed') as $allowance)

                        <label class="block">

                            <input type="checkbox"
                                name="allowances[]"
                                value="{{ $allowance->id }}">

                            {{ $allowance->name }}

                            （{{ number_format($allowance->amount) }}円）

                        </label>

                        @endforeach

                    </div>

                    {{-- 作業手当 --}}
                    <div>

                        <div class="font-bold text-green-600 mb-2">

                            作業手当対象

                        </div>

                        @foreach($allowances->where('type', 'work') as $allowance)

                        <label class="block">

                            <input type="checkbox"
                                name="allowances[]"
                                value="{{ $allowance->id }}">

                            {{ $allowance->name }}

                            （{{ number_format($allowance->amount) }}円）

                        </label>

                        @endforeach

                    </div>

                </div>

            </div>

            <button class="bg-blue-500 text-white px-5 py-2 rounded">

                登録

            </button>

        </form>

    </div>

</x-app-layout>