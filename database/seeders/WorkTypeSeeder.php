<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkType;

class WorkTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workTypes = [
            '解体工',
            '重機',
            '重機2',
            'はつり',
            '石綿',
            'トラック',
            'ユニック',
        ];

        foreach ($workTypes as $workType) {

            WorkType::create([
                'name' => $workType,
            ]);

        }
    }
}