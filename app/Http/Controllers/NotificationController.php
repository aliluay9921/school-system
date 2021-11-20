<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Traits\Pagination;
use App\Traits\SendNotificationFirebase;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
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
        return $this->send_notification_firebase("غياب", "غياب", "eK4lCemISbKZAjZ63tfGOp:APA91bH1LWaxbi6WTPYvQNWQYmpP04tArNoLKdsedHYMohYUGRuVSj0xLcAUeiTVpqZpwoSsbBia1X5BiQBg2V525vgZVg3gsz7jBaN7hk1jF597grR-qvRkNGMb2MFtWyU2taP9G0MA");
    }
}