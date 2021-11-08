<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Stage;
use App\Models\Degree;
use App\Models\School;
use App\Models\Material;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class DegreeSeeder extends Seeder
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
        $user = User::where('user_type', 3)->first();
        $semester = Semester::first();
        Degree::create([
            'material_id' => $material->id,
            'user_id' => $user->id,
            'class_id' => $stage->id,
            'semester_id' => $semester->id,
            'degree' => 10,
            'school_id' => $school->id,
        ]);
    }
}
