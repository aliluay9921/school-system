<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SemesterController extends Controller
{
    use SendResponse;
    public function addSemester(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'name' => 'required|unique:semesters,name',
            'max_degree' => 'required',
            'class_id' => 'required',
        ], [
            'name.required' => 'يجب ادخال اسم الفصل ',
            'max_degree.required' => 'يجب ادخال معدل الدرجات  ',
            'class_id.required' => 'يجب ادخال  الصف  ',
            'name.unique' => 'اسم الفصل مستخدم سابقاً',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }

        $semester = Semester::create([
            'name' => $request['name'],
            'max_degree' => $request['max_degree'],
            'class_id' => $request['class_id'],
            'school_id' => auth()->user()->school->id,
        ]);
        return $this->send_response(200, 'تم اضافة فصل جديد', [], Semester::find($semester->id));
    }
}