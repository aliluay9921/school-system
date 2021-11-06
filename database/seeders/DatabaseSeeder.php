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
        $this->call(StageSeeder::class);
        $this->call(UserSeeder::class);
    }
}