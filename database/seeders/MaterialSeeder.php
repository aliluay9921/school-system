<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $school = School::first();

        Material::Create([
            'name' => 'arbic',
            'school_id' => $school->id,
        ]);

        Material::Create([
            'name' => 'math',
            'school_id' => $school->id,
        ]);
    }
}
