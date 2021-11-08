<?php

namespace Database\Seeders;

use App\Models\Stage;
use App\Models\School;
use App\Models\Material;
use App\Models\DailyMaterial;
use Illuminate\Database\Seeder;

class DailyMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stage = Stage::first();
        $material = Material::get();
        $school = School::first();

        DailyMaterial::create([
            'class_id' => $stage->id,
            'materials' => [$material[0]->id, $material[1]->id],
            'day' => 'sunday',
            'school_id' => $school->id,

        ]);
    }
}
