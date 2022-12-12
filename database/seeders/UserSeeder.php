<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $school = School::first();
        $stage = Stage::first();
        User::create([
            'full_name' => 'علي لؤي',
            'user_name' => 'micodev',
            'password' => bcrypt('11111111'),
            'gender' => 0,
            'user_type' => 0,
        ]);
        // User::create([
        //     'school_id' => $school->id,
        //     'full_name' => 'ابراهيم اياد المدير',
        //     'user_name' => 'ibrahim_ayad',
        //     'password' => bcrypt('11111111'),
        //     'gender' => 0,
        //     'user_type' => 1,
        //     'salary' => 900,
        //     'address' => 'بغداد-الشعب',
        // ]);
        // User::create([
        //     'school_id' => $school->id,
        //     'full_name' => 'يوسف المدرس',
        //     'user_name' => 'yousuf_teacher',
        //     'password' => bcrypt('11111111'),
        //     'gender' => 0,
        //     'user_type' => 2,
        //     'salary' => 900,
        //     'address' => 'بغداد-الشعب',
        // ]);
        // User::create([
        //     'school_id' => $school->id,
        //     'full_name' => ' محسن طالب',
        //     'user_name' => 'mohsen_student',
        //     'password' => bcrypt('11111111'),
        //     'gender' => 0,
        //     'parent_job' => 'كاسب',
        //     'user_type' => 3,
        //     'class_id' => $stage->id,
        //     'address' => 'بغداد-الشعب',
        // ]);
    }
}
