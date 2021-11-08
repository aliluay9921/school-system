<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Material;
use App\Models\School;
use App\Models\Stage;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stage = Stage::first();
        $material = Material::first();
        $school = School::first();
        Exam::create([
            'school_id' => $school->id,
            'class_id' => $stage->id,
            'material_id' => $material->id,
            'lesson_number' => 3,
            'day' => 'sunday',
            'date' => '2020/2/12',
        ]);
    }
}
