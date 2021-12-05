<?php

namespace App\Http\Controllers;

use App\Models\Degree;
use App\Models\Material_stage_teacher;
use App\Models\Semester;
use App\Models\User;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DegreeController extends Controller
{
    use SendResponse, Pagination;

    public function getDegrees()
    {
        if (auth()->user()->user_type == 3) {  //student
            if (auth()->user()->hide_degree == true) {
                return $this->send_response(500, "يجب عليك تسديد القسط اولاً", [], []);
            } else {
                $degrees = Degree::with('material', 'stage', 'semester', 'user')->where('school_id', auth()->user()->school->id)->where('user_id', auth()->user()->id);
            }
        } else {
            //front
            $degrees = Degree::with('material', 'stage', 'semester', 'user')->where('school_id', auth()->user()->school->id);
            if (isset($_GET['filter'])) {
                $filter = json_decode($_GET['filter']);
                $degrees->whereIn($filter->name, $filter->value); // array
            }
            return $this->send_response(200, 'تم جلب الدرجات بنجاح', [], $degrees->get(), null, 1);
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'filter') {
                    continue;
                } else {
                    $degrees->where($key, $value);
                }
            }
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 90;
        $res = $this->paging($degrees->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب الدرجات بنجاح', [], $res["model"], null, $res["count"]);
    }


    public function addDegree(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'user_id' => 'required|exists:users,id',
            'certificate' => 'required'
        ], [
            'user_id.required' => 'يجب ادخال  الطالب ',
            'user_id.exists' => 'يجب ادخال  طالب موجود ',
            'certificate.required' => 'يجب ادخال الدرجة',

        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        // $semester = Semester::find($request['semester_id']);
        // if ($semester->max_degree < $request['degree']) {
        //     return $this->send_response(401, 'يجب ادخال درجة مناسبة للفصل الدراسي', [], []);
        // }
        $user = User::find($request['user_id']);


        $materials = Material_stage_teacher::select('material_id', 'class_id')->where("class_id", $user->class_id)->get();
        $semesters = Semester::where("class_id", $user->class_id)->get();
        for ($i = 0; $i < count($request['certificate']); $i++) {
            $current_material = $materials[$i];
            for ($j = 0; $j < count($request['certificate'][$i]); $j++) {
                $current_semester = $semesters[$j];
                if ($request['certificate'][$i][$j] && $current_semester->max_degree < $request['certificate'][$i][$j]) {
                    return $this->send_response(401, 'يجب ادخال درجة أقل او تساوي  ' . $current_semester->max_degree, [], []);
                }
                $current_degree = $request['certificate'][$i][$j];
                $degree = Degree::where(
                    'material_id',
                    $current_material->material_id
                )->where(
                    'user_id',
                    $request['user_id']
                )->where("semester_id", $current_semester->id)->where(
                    'class_id',
                    $user->class_id
                )->first();

                if ($degree) {
                    $degree->update(["degree" => $current_degree]);
                } else {
                    $degree = Degree::create([
                        'material_id' => $current_material->material_id,
                        'user_id' => $request['user_id'],
                        'class_id' =>  $user->class_id,
                        'semester_id' => $current_semester->id,
                        'degree' => $current_degree,
                        'school_id' => auth()->user()->school->id,
                    ]);
                }
            }
        }
        return $this->send_response(200, 'تم اضافة الدرجة بنجاح', [], []);
    }

    public function hideDegree(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            "user_id" => "required|exists:users,id"
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }

        $user = User::find($request["user_id"]);
        $user->update(["hide_degree" => !$user->hide_degree]);
        return $this->send_response(200, "تم اخفاء النتيجة بنجاح", [], User::find($user->id));
    }
}