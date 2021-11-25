<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StageController extends Controller
{
    use SendResponse, Pagination;

    public function getStages()
    {
        if (isset($_GET['class_id'])) {
            $stage = Stage::with('users', 'users.degrees')->find($_GET['class_id']);
            return $this->send_response(200, 'تم جلب معلومات الصف ', [], $stage);
        }
        $stages = Stage::with('semesters')->where("school_id", auth()->user()->school_id)->withCount('users');
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit') {
                    continue;
                } else {
                    $stages->where($key, $value);
                }
            }
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($stages,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب المستخدمين بنجاح', [], $res["model"], null, $res["count"]);
    }

    public function addStage(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'name' => 'required|unique:stages,name',
            'fee' => 'required',
        ], [
            'name.required' => 'يجب ادخال اسم الصصف ',
            'name.unique' => 'اسم الصف مستخدم سابقاً',
            'fee.required' => 'يجب ادخال  القسط المخصص',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $stage = Stage::create([
            'name' => $request['name'],
            'fee' => $request['fee'],
            'school_id' => auth()->user()->school->id,
        ]);
        return $this->send_response(200, 'تم اضافة صف بنجاح', [], Stage::find($stage->id));
    }
}