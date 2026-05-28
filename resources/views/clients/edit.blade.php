<x-app-layout>

    <div class="max-w-3xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-5">
            元請け登録
        </h1>

        <form action="{{ route('clients.update', $client) }}"
            method="POST"
            class="space-y-5">
            @method('PUT')
            @csrf

            <div>
                <label class="block mb-1">名前</label>
                <input type="text"
                    name="name"
                    class="w-full border rounded p-2">
            </div>

            @php

            $fields = [
            'demolition_unit_price' => '解体単価',
            'heavy_equipment_unit_price' => '重機単価',
            'heavy_equipment2_unit_price' => '重機2単価',
            'chipping_unit_price' => 'はつり単価',
            'asbestos_unit_price' => '石綿単価',
            'truck_unit_price' => 'トラック単価',
            'unic_unit_price' => 'ユニック単価',
            ];

            @endphp

            @foreach($fields as $field => $label)

            <div>

                <label class="block mb-1">
                    {{ $label }}
                </label>

                <input type="number"
                    name="{{ $field }}"
                    class="w-full border rounded p-2"
                    value="{{ old($field, $client->$field) }}">

            </div>

            @endforeach

            <button class="bg-blue-600 text-white px-5 py-2 rounded">

                登録

            </button>

        </form>

    </div>

</x-app-layout>