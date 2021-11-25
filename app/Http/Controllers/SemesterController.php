<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;




class SemesterController extends Controller
{
    use SendResponse, Pagination;
    public function getSemesters()
    {
        $semester = Semester::with("stage")->where('school_id', auth()->user()->School->id);
        if (isset($_GET['query'])) {
            $semester->where(function ($q) {
                $columns = Schema::getColumnListing('semesters');
                $q->whereHas('stage', function ($q) {
                    $q->Where('name', 'LIKE', '%' . $_GET['query'] . '%');
                });
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
                }
            });
        }
        if (isset($_GET['filter'])) {
            $filter = json_decode($_GET['filter']);
            $semester->whereIn($filter->name, $filter->value);
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query' || $key == 'filter') {
                    continue;
                } else {
                    $sort = $value == 'true' ? 'desc' : 'asc';
                    $semester->orderBy($key,  $sort);
                }
            }
        }

        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($semester,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب المستخدمين بنجاح', [], $res["model"], null, $res["count"]);
    }
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
        return $this->send_response(200, 'تم اضافة فصل جديد', [], Semester::with("stage")->find($semester->id));
    }

    public function deleteSemester(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'semester_id' => 'required|exists:semesters,id'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }

        Semester::find($request['semester_id'])->delete();
        return $this->send_response(200, 'تم حذف الفصل', [], []);
    }
}