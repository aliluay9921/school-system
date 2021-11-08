<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $school = School::first();
        $user = User::first();
        Payment::create([
            'school_id' => $school->id,
            'pay_date' => '2020/2/22',
            'value' => '500000',
            'user_id' => $user->id
        ]);
    }
}
