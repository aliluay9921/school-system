<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    use SendResponse, Pagination;

    public function getMaterials()
    {
        $materials = Material::where('school_id', auth()->user()->school->id);
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($materials,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب الدروس بنجاح', [], $res["model"], null, $res["count"]);
    }



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
