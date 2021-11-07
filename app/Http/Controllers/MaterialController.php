<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    use SendResponse;
    public function addMaterial(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'name' => 'required',
        ], [
            'name.required' => 'يجب ادخال اسم الفصل ',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $material = Material::Create([
            'name' => $request['name'],
            'school_id' => auth()->user()->school->id,
        ]);
        return $this->send_response(200, 'تم اضافة مادة جديدة', [], Material::find($material->id));
    }
}