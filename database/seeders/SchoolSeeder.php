<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Stage;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        School::create([
            'name' => 'مدرسة الابتهار الاهلية',
            'address' => 'بغداد-الشعب',
            'student_number' => 500
        ]);
        School::create([
            'name' => 'مدرسة التمني الاهلية',
            'address' => 'بغداد-شارع فلسطين',
            'student_number' => 300
        ]);
        School::create([
            'name' => 'مدرسة الطموح الاهلية',
            'address' => 'بغداد-شارع فلسطين',
            'student_number' => 300
        ]);
    }
}