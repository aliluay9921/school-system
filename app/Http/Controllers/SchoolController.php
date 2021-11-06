<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Traits\SendResponse;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    use SendResponse;
    public function add_school(Request $request)
    {
        $request = $request->json()->all();
        $school = School::create([
            'name' => $request['name'],
            'address' => $request['address'],
            'student_number' => $request['student_number'],
        ]);
        return $this->send_response(200, 'تم اضافة مدرسة جديدة', School::find($school->id));
    }
}