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
        if (auth()->user()->user_type == 3) {
            $notification = Notification::with(["users" => function ($q) {
                $q->where("user.id", auth()->user()->id);
            }])->where("school_id", auth()->user()->school_id);
        }
        return $notification;
    }
}