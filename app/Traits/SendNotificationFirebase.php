<?php

namespace App\Traits;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

trait SendNotificationFirebase
{
    public function send_notification_firebase($title, $body, $token)
    {
        $factory = (new Factory)->withServiceAccount(__DIR__ . './firebase.json');
        $messaging = $factory->createMessaging();
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body))
            ->withData(['token' => $token]); // هذا معناها الشخص من يدوس ع اشعار يودي للمكان الي اني انطي هنا 


        return  $messaging->send($message);
    }
}