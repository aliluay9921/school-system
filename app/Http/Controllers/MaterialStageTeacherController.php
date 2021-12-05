<?php

namespace App\Http\Controllers;

use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use App\Models\Material_stage_teacher;
use Database\Seeders\MaterialStageTeacherSeeder;
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
        // if (isset($_GET)) {
        //     foreach ($_GET as $key => $value) {
        //         if ($key == 'skip' || $key == 'limit' || $key == 'query') {
        //             continue;
        //         } else {
        //             $get->where($key, $value);
        //         }
        //     }
        // }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query') {
                    continue;
                } else {
                    $sort = $value == 'true' ? 'desc' : 'asc';
                    $get->orderBy($key,  $sort);
                }
            }
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($get->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب توزيع الدروس  بنجاح', [], $res["model"], null, $res["count"]);
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
        $smt = Material_stage_teacher::where("school_id", auth()->user()->school->id)->where("class_id", $request["class_id"])->where("teacher_id", $request["teacher_id"])->where("material_id", $request["material_id"])->get();
        // return $smt;
        if ($smt->count() > 0) {
            return $this->send_response("401", "لقد قمت بأضافة هذه المادة لهذا المدرس في هذاالصف", [], []);
        }
        $add = Material_stage_teacher::create([
            'class_id' => $request['class_id'],
            'teacher_id' => $request['teacher_id'],
            'material_id' => $request['material_id'],
            'school_id' => auth()->user()->school->id
        ]);
        return $this->send_response(200, 'تم اضافة مدرس الى صف ومادة', [], Material_stage_teacher::find($add->id));
    }

    public function deleteMaterialStageTeacher(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'id' => 'required|exists:material_stage_teachers,id'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        Material_stage_teacher::find($request['id'])->delete();
        return $this->send_response(200, 'تم حذف ', [], []);
    }
}