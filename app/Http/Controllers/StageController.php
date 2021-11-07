<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StageController extends Controller
{
    use SendResponse;
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
