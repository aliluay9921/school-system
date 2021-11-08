<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\School;
use App\Models\Feedback;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $school = School::first();

        $user = User::where('user_type', 3)->first();
        Feedback::create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'text' => 'مدرسة جيدة'
        ]);
        Feedback::create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'text' => 'مدرسة فاشلة'
        ]);
    }
}
