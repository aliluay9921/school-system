<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    use SendResponse;
    public function addUser(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'user_name' => 'required|unique:users,user_name',
            'password' => 'required|min:6',
            'full_name' => 'required',
            'gender' => 'required',
            'class_id' => $request['user_type'] == 3 ? 'required|exists:users,id' : "",
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
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $data = [];
        $data = [
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
        return $this->send_response(200, 'تم اضافة مستخدم بنجاح', [], $user);
    }
}