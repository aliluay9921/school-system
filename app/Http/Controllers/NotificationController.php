<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use SendResponse, Pagination;
    public function getNotification()
    {

        $notification = Notification::with("issuer", "comment", "dailyMaterial")->whereHas("users", function ($q) {
            $q->where("users.id", auth()->user()->id);
        });
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($notification,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب الاشعارات بنجاح', [], $res["model"], null, $res["count"]);
    }
}