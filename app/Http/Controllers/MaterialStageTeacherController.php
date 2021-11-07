<?php

namespace App\Http\Controllers;

use App\Models\Material_stage_teacher;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialStageTeacherController extends Controller
{
    use SendResponse;
    public function addMaterialStageTeacher(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'material_id' => 'required|exists:materials,id',
            'teacher_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:stages,id',
        ], [
            'material_id.required' => 'يجب ادخال المادة  ',
            'teacher_id.required' => 'يجب ادخال  التدريسي ',
            'class_id.required' => 'يجب ادخال الصف ',
            'material_id.exists' => 'يجب ادخال مادة موجودة  ',
            'teacher_id.exists' => 'يجب ادخال  تدريسي موجود ',
            'class_id.exists' => 'يجب ادخال صف موجود ',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $add = Material_stage_teacher::create([
            'class_id' => $request['class_id'],
            'teacher_id' => $request['teacher_id'],
            'material_id' => $request['material_id'],
            'school_id' => auth()->user()->school->id
        ]);
        return $this->send_response(200, 'تم اضافة مدرس الى صف ومادة', [], Material_stage_teacher::find($add->id));
    }
}