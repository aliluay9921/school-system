<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    use SendResponse, Pagination;

    public function getMaterials()
    {
        $materials = Material::where('school_id', auth()->user()->school->id);
        if (isset($_GET['query'])) {
            $materials->where(function ($q) {
                $columns = Schema::getColumnListing('materials');
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
                }
            });
        }
        if (isset($_GET['filter'])) {
            $materials->whereHas("materials_stages_teachers", function ($q) {
                $filter = json_decode($_GET['filter']);
                $q->whereIn($filter->name, $filter->value);
            });
            // $filter = json_decode($_GET['filter']);
            // $materials->whereIn($filter->name, $filter->value);
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query' || $key == 'filter') {
                    continue;
                } else {
                    $sort = $value == 'true' ? 'desc' : 'asc';
                    $materials->orderBy($key,  $sort);
                }
            }
        }
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
    public function deleteMaterial(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'material_id' => 'required|exists:materials,id'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }

        Material::find($request['material_id'])->delete();
        return $this->send_response(200, 'تم حذف المادة', [], []);
    }
}
