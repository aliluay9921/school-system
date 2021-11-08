<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Semester;
use App\Models\Stage;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $school = School::first();
        $class = Stage::first();

        Semester::create([
            'name' => 'فصل اول',
            'max_degree' => 10,
            'class_id' => $class->id,
            'school_id' => $school->id,
        ]);
        Semester::create([
            'name' => 'فصل ثاني',
            'max_degree' => 10,
            'class_id' => $class->id,
            'school_id' => $school->id,
        ]);
    }
}
