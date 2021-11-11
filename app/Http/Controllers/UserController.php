<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    use SendResponse, Pagination;

    public function getUsers()
    {
        if (isset($_GET['user_id'])) {
            $user = User::with('degrees', 'payments')->find($_GET['user_id']);
            return $this->send_response(200, 'تم جلب معلومات المستخدم بنجاح', [], $user);
        }

        $users = User::with('stage')->where('school_id', auth()->user()->School->id)->where('user_type', $_GET['user_type']);
        if (isset($_GET['query'])) {
            $users->where(function ($q) {
                $columns = Schema::getColumnListing('users');
                $q->whereHas('stage', function ($q) {
                    $q->Where('name', 'LIKE', '%' . $_GET['query'] . '%');
                });
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
                }
            });
        }
        if (isset($_GET['filter'])) {
            $filter = json_decode($_GET['filter']);
            $users->whereIn($filter->name, $filter->value);
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query' || $key == 'filter') {
                    continue;
                } else {
                    $sort = $value == 'true' ? 'desc' : 'asc';
                    $users->orderBy($key,  $sort);
                }
            }
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($users,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب المستخدمين بنجاح', [], $res["model"], null, $res["count"]);
    }

    public function addUser(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'user_name' => 'required|unique:users,user_name',
            'password' => 'required|min:6',
            'full_name' => 'required',
            'gender' => 'required',
            'class_id' => $request['user_type'] == 3 ? 'required|exists:stages,id' : "",
            'user_type' => 'required',
        ], [
            'user_name.required' => 'يجب ادخال اسم المستخدم',
            'user_name.unique' => 'اسم المستخدم موجود سابقاً',
            'password.required' => 'يجب ادخال كلمة مرور',
            'password.min' => 'يجب ان تكون كلمة المرور على الاقل 6',
            'full_name.required' => 'يجب ادخال اسم الكامل',
            'gender.required' => 'يجب ادخال  جنس',
            'user_type.required' => 'يجب ادخال نوع المستخدم ',
            'class_id.exists' => 'يجب ادخال صف صحيح',
            'class_id.required' => 'يجب ادخال صف ',
            'school_id.exists' => 'يجب ادخال مدرسة موجودة',
            'school_id.required' => 'يجب ادخال المدرسة ',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $data = [];
        $data = [
            'school_id' => auth()->user()->School->id,
            'full_name' => $request['full_name'],
            'user_name' => $request['user_name'],
            'password' => bcrypt($request['password']),
            'gender' => $request['gender'],
            'user_type' => $request['user_type'],
        ];
        if (array_key_exists('address', $request)) {
            $data['address'] = $request['address'];
        }
        if (array_key_exists('phone_number', $request)) {
            $data['phone_number'] = $request['phone_number'];
        }
        if (array_key_exists('birth_day', $request)) {
            $data['birth_day'] = $request['birth_day'];
        }
        if (array_key_exists('discount_value', $request)) {
            $data['discount_value'] = $request['discount_value'];
        }
        if (array_key_exists('parent_job', $request)) {
            $data['parent_job'] = $request['parent_job'];
        }
        if (array_key_exists('salary', $request)) {
            $data['salary'] = $request['salary'];
        }
        if (array_key_exists('class_id', $request)) {
            $data['class_id'] = $request['class_id'];
        }
        $user = User::create($data);
        return $this->send_response(200, 'تم اضافة مستخدم بنجاح', [], User::find($user->id));
    }


    public function deleteUser(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'user_id' => 'required|exists:users,id'
        ], [
            'user_id.required' => 'يجب ادخال  المستخدم المراد حذفه',
            'user_id.exists' => 'المستخدم الذي قمت بأدخاله غير موجود',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }

        $user = User::find($request['user_id']);
        $user->delete();
        return $this->send_response(200, 'تم حذف المستخدم', [], []);
    }
}