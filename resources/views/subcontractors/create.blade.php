<x-app-layout>

    <div class="max-w-3xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-5">
            下請け登録
        </h1>

        <form action="{{ route('subcontractors.store') }}"
            method="POST"
            class="space-y-5">

            @csrf

            <div>

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

            <button class="bg-blue-600 text-white px-5 py-2 rounded">

                登録

            </button>

        </form>

    </div>

</x-app-layout>