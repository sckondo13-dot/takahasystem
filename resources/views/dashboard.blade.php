<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('メニュー一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-dropdown-link :href="route('daily-reports.index')">
                        日報管理
                    </x-dropdown-link>
                    <x-dropdown-link :href="route('attendance.index')">
                        個人出勤簿
                    </x-dropdown-link>
                    <x-dropdown-link :href="route('sites.index')">
                        現場管理
                    </x-dropdown-link>

                    <x-dropdown-link :href="route('employees.index')">
                        従業員管理
                    </x-dropdown-link>
                    <x-dropdown-link :href="route('allowances.index')">
                        手当管理
                    </x-dropdown-link>

                    <x-dropdown-link :href="route('clients.index')">
                        元請け管理
                    </x-dropdown-link>

                    <x-dropdown-link :href="route('subcontractors.index')">
                        下請け管理
                    </x-dropdown-link>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>