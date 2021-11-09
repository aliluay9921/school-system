<?php

namespace Database\Seeders;

use App\Http\Controllers\UserController;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(SchoolSeeder::class);
        $this->call(MaterialSeeder::class);
        $this->call(StageSeeder::class);
        $this->call(UserSeeder::class);

        $this->call(DailyMaterialSeeder::class);
        $this->call(PaymentSeeder::class);
        $this->call(SemesterSeeder::class);
        $this->call(FeedbackSeeder::class);
        $this->call(SemesterSeeder::class);
        $this->call(ExamSeeder::class);

        $this->call(MaterialStageTeacherSeeder::class);
    }
}
