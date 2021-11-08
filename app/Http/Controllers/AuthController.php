<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    use SendResponse;

    public function login(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'user_name' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        if (Auth::attempt(['user_name' => $request['user_name'], 'password' => $request['password']])) {
            $user = Auth::user();
            $token = $user->createToken($user->user_name)->accessToken;
            return $this->send_response(200, 'تم تسجيل الدخول بنجاح', [], User::with('school', 'stage', 'materials_stages_teachers')->find($user->id), $token);
        } else {
            return $this->send_response(401, 'هناك مشكلة تحقق من تطابق المدخلات', null, null, null);
        }
    }

    public function authInfo(Request $request)
    {
        return $this->send_response(200, 'تم جلب معلومات المستخدم', [], User::with('school', 'stage', 'materials_stages_teachers', 'payments')->find(auth()->user()->id));
    }
}
