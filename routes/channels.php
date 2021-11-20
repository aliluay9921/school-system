<?php

use App\Models\Report;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });


Broadcast::channel('user_notification.{id}', function ($user, $id) {
    return  $user->id === $id;
});
Broadcast::channel('absent_report.{id}', function ($user, $id) {
    return  $user->id === $id;
});
Broadcast::channel('general_reports.{school_id}', function ($user, $school_id) {
    error_log($school_id);
    error_log($user->school_id);
    return $user->school_id === $school_id;
});

Broadcast::channel('class_report.{class_id}', function ($user, $class_id) {
    return  $user->class_id === $class_id;
});
Broadcast::channel('comment_socket.{report_id}', function ($user, $report_id) {
    // $reports = Report::where('id', $report_id)->where(function ($q) use ($user) {
    //     $q->Where('issuer_id', $user->id)->orWhere('user_id', $user->id);
    // })->get();
    return true; // count($reports) > 0;
});