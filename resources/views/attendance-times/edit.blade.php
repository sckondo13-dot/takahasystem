<x-app-layout>

    <div class="max-w-3xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-5">

            出退勤時間編集

        </h1>

        <form action="{{ route('attendance-times.update',$attendanceTime) }}"
            method="POST">

            @csrf
            @method('PUT')

            <div class="mb-4">

                <label>

                    項目名

                </label>

                <input
                    type="text"
                    name="name"
                    class="w-full border rounded p-2"
                    value="{{ old('name',$attendanceTime->name) }}">

            </div>

            <div class="mb-4">

                <label>

                    始業時間

                </label>

                <input
                    type="time"
                    name="start_time"
                    class="w-full border rounded p-2"
                    value="{{ old('start_time',substr($attendanceTime->start_time,0,5)) }}">

            </div>

            <div class="mb-4">

                <label>

                    終業時間

                </label>

                <input
                    type="time"
                    name="end_time"
                    class="w-full border rounded p-2"
                    value="{{ old('end_time',substr($attendanceTime->end_time,0,5)) }}">

            </div>

            <div class="mb-4">

                <label>

                    備考

                </label>

                <textarea
                    name="note"
                    class="w-full border rounded p-2">{{ old('note',$attendanceTime->note) }}</textarea>

            </div>

            <button
                class="bg-blue-600 text-white px-5 py-2 rounded">

                更新

            </button>

        </form>

    </div>

</x-app-layout>