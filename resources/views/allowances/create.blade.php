<x-app-layout>

    <div class="max-w-3xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-5">
            手当登録
        </h1>

        <form action="{{ route('allowances.store') }}"
              method="POST">

            @csrf

            <div class="mb-4">

                <label>
                    名称
                </label>

                <input type="text"
                       name="name"
                       class="w-full border rounded p-2">

            </div>

            <div class="mb-4">

                <label>
                    種別
                </label>

                <select name="type"
                        class="w-full border rounded p-2">

                    <option value="fixed">
                        固定手当
                    </option>

                    <option value="work">
                        作業手当
                    </option>

                </select>

            </div>

            <div class="mb-4">

                <label>
                    金額
                </label>

                <input type="number"
                       name="amount"
                       class="w-full border rounded p-2">

            </div>

            <div class="mb-4">

                <label>
                    備考
                </label>

                <textarea name="note"
                          class="w-full border rounded p-2"></textarea>

            </div>

            <button class="bg-blue-600 text-white px-5 py-2 rounded">

                登録

            </button>

        </form>

    </div>

</x-app-layout>