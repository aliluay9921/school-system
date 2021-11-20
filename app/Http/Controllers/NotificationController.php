<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Pagination;
use App\Models\Notification;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use App\Traits\SendNotificationFirebase;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    use SendResponse, Pagination, SendNotificationFirebase;
    public function getNotification()
    {

        $notification = Notification::whereHas("users", function ($q) {
            $q->where("users.id", auth()->user()->id);
        });
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($notification,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب الاشعارات بنجاح', [], $res["model"], null, $res["count"]);
    }
    public function seen(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            "notify_id" => 'required|exists:notifications,id'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $notification = Notification::find($request['notify_id']);
        $notification->update([
            'seen' => true
        ]);
        return $this->send_response(200, "تم المشاهدة", [], []);
    }

    public function sendFirebase()
    {
        $user = User::find("bfbb14ea-33f9-4626-8309-4202ac8ccbe3");
        foreach ($user->firebaseTokens as $token) {
            $this->send_notification_firebase("غياب", "غياب", $token->token);
        }
    }
}