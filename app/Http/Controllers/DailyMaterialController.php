<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Pagination;
use App\Models\Notification;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use App\Models\DailyMaterial;
use Illuminate\Support\Facades\Validator;

class DailyMaterialController extends Controller
{
    use SendResponse, Pagination;

    public function getDailyMaterials()
    {
        if (auth()->user()->user_type == 3) {
            $daily_materials = DailyMaterial::with('stage')->where('school_id', auth()->user()->School->id)->where('class_id', auth()->user()->class_id);
        } else {
            $daily_materials = DailyMaterial::with('stage')->where('school_id', auth()->user()->School->id);
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit') {
                    continue;
                } else {
                    $daily_materials->where($key, $value);
                }
            }
        }

        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($daily_materials,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب جدول الدروس بنجاح', [], $res["model"], null, $res["count"]);
    }


    public function addDailyMaterial(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'class_id' => 'required|exists:stages,id',
            'materials' => 'required',
            'day'     => 'required'
        ], [
            'class_id.required' => 'يجب ادخال الصف',
            'class_id.exists' => 'يجب ادخال صف صحيح',
            'materials.required' => 'يجب ادخال مواد',
            'day.required' => 'يجب ادخال اليوم',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        // return $request['materials'];
        $daily_materials = DailyMaterial::create([
            'class_id' => $request['class_id'],
            'materials' => $request['materials'],
            'day' => $request['day'],
            'school_id' => auth()->user()->school->id,

        ]);
        return $this->send_response(200, 'تم اضافة جدول يومي', [], DailyMaterial::find($daily_materials->id));
    }

    public function editDailyMaterial(Request $request)
    {
        $request = $request->json()->all();
        $daily_material = DailyMaterial::find($request['daily_material_id']);
        $validator = Validator::make($request, [
            'daily_material_id' => 'required|exists:daily_materials,id',
            'class_id' => 'required|exists:stages,id',
            'materials' => 'required',
            'day'     => 'required'
        ], [
            'class_id.required' => 'يجب ادخال الصف',
            'class_id.exists' => 'يجب ادخال صف صحيح',
            'materials.required' => 'يجب ادخال مواد',
            'day.required' => 'يجب ادخال اليوم',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $daily_material->update([
            'class_id' => $request['class_id'],
            'materials' => $request['materials'],
            'day' => $request['day'],
        ]);
        $notify = Notification::create([
            "title" => "تم تغير الجدول الاسبوعي  ",
            "body"  => "يرجى الاطلاع على الجدول لمعرفة التغير الحاصل",
            "from"  => auth()->user()->id,
            "target_id" => $request['daily_material_id'],
            "type"  => 1,
            "school_id" => auth()->user()->school->id

        ]);
        $users = User::where('school_id', auth()->user()->school->id)->where('user_type', 3)->get();
        foreach ($users as $user) {
            $notify->users()->attach($user);
        }
        return $this->send_response(200, "تم التعديل على الجدول", [], DailyMaterial::find($request['daily_material_id']));
    }

    public function deleteDailyMaterial(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'daily_material_id' => 'required|exists:daily_materials,id'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        DailyMaterial::find($request['daily_material_id'])->delete();
        return $this->send_response(200, 'تم حذف الجدول', [], []);
    }
}