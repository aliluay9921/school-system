<?php

namespace App\Http\Controllers;

use App\Models\DailyMaterial;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DailyMaterialController extends Controller
{
    use SendResponse, Pagination;

    public function getDailyMaterials()
    {
        $daily_materials = DailyMaterial::with('stage')->where('school_id', auth()->user()->School->id);
        if (isset($_GET['class_id'])) {
            $daily_materials->where('class_id', $_GET['class_id']);
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
            'day'     => 'required|date'
        ], [
            'class_id.required' => 'يجب ادخال الصف',
            'class_id.exists' => 'يجب ادخال صف صحيح',
            'materials.required' => 'يجب ادخال مواد',
            'day.required' => 'يجب ادخال اليوم',
            'day.date' => 'غلط في ادخال التأريخ'
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
