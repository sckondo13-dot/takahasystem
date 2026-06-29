<x-app-layout>

    <div class="max-w-5xl mx-auto py-10">

        <div class="flex justify-between mb-5">

            <h1 class="text-2xl font-bold">

                出退勤時間マスタ

            </h1>

            <a href="{{ route('attendance-times.create') }}"
                class="bg-blue-600 text-white px-5 py-2 rounded">

                新規登録

            </a>

        </div>

        <table class="w-full border">

            <thead class="bg-gray-100">

                <tr>

                    <th class="border p-2">名称</th>

                    <th class="border p-2">始業</th>

                    <th class="border p-2">終業</th>

                    <th class="border p-2">備考</th>
                    <th class="border p-2">
                        操作
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($attendanceTimes as $attendanceTime)

                <tr>

                    <td class="border p-2">

                        {{ $attendanceTime->name }}

                    </td>

                    <td class="border p-2">

                        {{ substr($attendanceTime->start_time,0,5) }}

                    </td>

                    <td class="border p-2">

                        {{ substr($attendanceTime->end_time,0,5) }}

                    </td>

                    <td class="border p-2">

                        {{ $attendanceTime->note }}

                    </td>
                    <td class="border p-2">

                        <div class="flex gap-2 justify-center">

                            <a href="{{ route('attendance-times.edit',$attendanceTime) }}"
                                class="bg-yellow-500 text-white px-3 py-1 rounded">

                                編集

                            </a>

                            <form
                                action="{{ route('attendance-times.destroy',$attendanceTime) }}"
                                method="POST"
                                onsubmit="return confirm('削除しますか？')">

                                @csrf
                                @method('DELETE')

                                <button
                                    class="bg-red-600 text-white px-3 py-1 rounded">

                                    削除

                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</x-app-layout>