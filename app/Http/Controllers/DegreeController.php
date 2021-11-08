<?php

namespace App\Http\Controllers;

use App\Models\Degree;
use App\Models\Semester;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DegreeController extends Controller
{
    use SendResponse, Pagination;

    // public function getDegrees()
    // {
    //     if (isset($_GET['class_id'])) {
    //         $degrees = Degree::with('material', 'stage', 'semester', 'user')->where('class_id', $_GET['class_id'])->get();
    //         return $this->send_response(200, 'تم جلب درجات', [], $degrees);
    //     }
    // }


    public function addDegree(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'material_id' => 'required|exists:materials,id',
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:stages,id',
            'semester_id' => 'required|exists:semesters,id',
            'degree' => 'required'
        ], [
            'material_id.required' => 'يجب ادخال المادة  ',
            'user_id.required' => 'يجب ادخال  الطالب ',
            'class_id.required' => 'يجب ادخال الصف ',
            'material_id.exists' => 'يجب ادخال مادة موجودة  ',
            'user_id.exists' => 'يجب ادخال  طالب موجود ',
            'class_id.exists' => 'يجب ادخال صف موجود ',
            'semester_id.required' => 'يجب ادخال الفصل الدراسي',
            'semester_id.exists' => 'يجب ادخال فصل دراسي صحيح',
            'degree.required' => 'يجب ادخال الدرجة',

        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $semester = Semester::find($request['semester_id']);
        if ($semester->max_degree < $request['degree']) {
            return $this->send_response(401, 'يجب ادخال درجة مناسبة للفصل الدراسي', [], []);
        }
        $degree = Degree::create([
            'material_id' => $request['material_id'],
            'user_id' => $request['user_id'],
            'class_id' => $request['class_id'],
            'semester_id' => $request['semester_id'],
            'degree' => $request['degree'],
            'school_id' => auth()->user()->school->id,
        ]);
        return $this->send_response(200, 'تم اضافة الدرجة بنجاح', [], Degree::with('material', 'semester', 'user', 'stage')->find($degree->id));
    }
}
