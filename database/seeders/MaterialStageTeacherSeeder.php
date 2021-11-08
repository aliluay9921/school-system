<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Stage;
use App\Models\School;
use App\Models\Material;
use App\Models\Semester;
use Illuminate\Database\Seeder;
use App\Models\Material_stage_teacher;

class MaterialStageTeacherSeeder extends Seeder
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
        $user = User::where('user_type', 2)->first();
        Material_stage_teacher::create([
            'class_id' => $stage->id,
            'teacher_id' => $user->id,
            'material_id' => $material->id,
            'school_id' => $school->id
        ]);
    }
}
