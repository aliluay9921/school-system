<?php

namespace App\Http\Controllers;

use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use App\Models\Material_stage_teacher;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class MaterialStageTeacherController extends Controller
{
    use SendResponse, Pagination;

    public function getMaterialStageTeacher()
    {
        $get = Material_stage_teacher::where('school_id', auth()->user()->School->id);

        if (isset($_GET['query'])) {
            $get->where(function ($q) {
                $columns = Schema::getColumnListing('material_stage_teachers');
                $q->whereHas('material', function ($q) {
                    $q->Where('name', 'LIKE', '%' . $_GET['query'] . '%');
                })->orWhereHas('user', function ($q) {
                    $q->Where('full_name', 'LIKE', '%' . $_GET['query'] . '%');
                })->orWhereHas('stage', function ($q) {
                    $q->Where('name', 'LIKE', '%' . $_GET['query'] . '%');
                });
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
                }
            });
        }

        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query') {
                    continue;
                } else {
                    $get->where($key, $value);
                }
            }
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($get,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب معلومات بنجاح', [], $res["model"], null, $res["count"]);
    }

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
