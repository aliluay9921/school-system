<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Stage;
use Illuminate\Database\Seeder;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $school = School::first();
        Stage::create([
            'name' => 'الصف الاؤل',
            'fee' => 1500000,
            'school_id' => $school->id
        ]);
        Stage::create([
            'name' => 'الصف الثاني',
            'fee' => 1500000,
            'school_id' => $school->id

        ]);
        Stage::create([
            'name' => 'الصف الثالث',
            'fee' => 1500000,
            'school_id' => $school->id

        ]);
        Stage::create([
            'name' => 'الصف الرابع',
            'fee' => 1500000,
            'school_id' => $school->id

        ]);
        Stage::create([
            'name' => 'الصف الخامس',
            'fee' => 1500000,
            'school_id' => $school->id

        ]);
        Stage::create([
            'name' => 'الصف السادس',
            'fee' => 1500000,
            'school_id' => $school->id

        ]);
    }
}