<?php

namespace App\Http\Controllers;

use App\Models\FirebaseToken;
use App\Models\User;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Kreait\Laravel\Firebase\Facades\Firebase;

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
            if (array_key_exists('token', $request)) {
                FirebaseToken::Create([
                    "user_id" => $user->id,
                    "token" => $request['token'],
                    "auth_token" => $token
                ]);
            }
            return $this->send_response(200, 'تم تسجيل الدخول بنجاح', [], User::with('school', 'stage', 'materials_stages_teachers')->find($user->id), $token);
        } else {
            return $this->send_response(401, 'هناك مشكلة تحقق من تطابق المدخلات', null, null, null);
        }
    }

    public function authInfo(Request $request)
    {
        return $this->send_response(200, 'تم جلب معلومات المستخدم', [], User::with('school', 'stage', 'materials_stages_teachers', 'payments')->find(auth()->user()->id));
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        $firebase = FirebaseToken::where("auth_token", $token)->first();
        auth()->user()->token()->revoke();
        $firebase->delete();
        return $this->send_response(200, 'تم تسجيل الخروج بنجاح', [], []);
    }
    public function replaceFirebaseToken(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            "old_token" => 'required|exists:firebase_tokens,token',
            "new_token" => 'required'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }

        $firebase = FirebaseToken::where("user_id", auth()->user()->id)->where("token", $request["old_token"])->first();
        if ($firebase) {
            $firebase->update([
                "token" => $request["new_token"]
            ]);
            return $this->send_response(200, "تم تحديث التوكن بنجاح", [], []);
        } else {
            return $this->send_response(401, "حدث خطأ", [], []);
        }
    }
}