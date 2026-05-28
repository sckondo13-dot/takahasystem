<x-app-layout>

    <div class="max-w-3xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-5">
            従業員編集
        </h1>

        <form action="{{ route('employees.update', $employee) }}"
            method="POST"
            class="space-y-5">

            @csrf
            @method('PUT')

            <div>

                <label class="block mb-1">
                    名前
                </label>

                <input type="text"
                    name="name"
                    class="w-full border rounded p-2"
                    value="{{ old('name', $employee->name) }}">

                @error('name')
                <div class="text-red-500 text-sm">
                    {{ $message }}
                </div>
                @enderror

            </div>

            <button class="bg-blue-500 text-white px-5 py-2 rounded">

                更新

            </button>

        </form>

    </div>

</x-app-layout>