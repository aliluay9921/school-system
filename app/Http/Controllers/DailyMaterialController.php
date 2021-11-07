<?php

namespace App\Http\Controllers;

use App\Models\DailyMaterial;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DailyMaterialController extends Controller
{
    use SendResponse;

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
        $daily_materials = DailyMaterial::create([
            'class_id' => $request['class_id'],
            'materials' => json_encode($request['materials']),
            'day' => $request['day'],
            'school_id' => auth()->user()->school->id,

        ]);
        return $this->send_response(200, 'تم اضافة جدول يومي', [], DailyMaterial::find($daily_materials->id));
    }
}