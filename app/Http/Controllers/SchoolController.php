<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    use SendResponse, Pagination;

    public function getSchool()
    {
        $schools = School::select('*')->withCount("users");

        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($schools,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب الدروس بنجاح', [], $res["model"], null, $res["count"]);
    }
    public function add_school(Request $request)
    {
        $request = $request->json()->all();
        $school = School::create([
            'name' => $request['name'],
            'address' => $request['address'],
        ]);
        return $this->send_response(200, 'تم اضافة مدرسة جديدة', [], School::find($school->id));
    }
}