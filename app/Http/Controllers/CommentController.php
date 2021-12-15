<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Image;
use App\Models\Report;
use App\Models\Comment;
use App\Traits\Pagination;
use App\Models\Notification;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use App\Events\CommentSocket;
use App\Events\AuthNotification;
use Illuminate\Support\Facades\Schema;
use App\Traits\SendNotificationFirebase;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    use SendResponse, Pagination, SendNotificationFirebase;

    public function getCommtns()
    {
        $comments = Comment::where('school_id', auth()->user()->School->id);
        if (isset($_GET['report_id'])) {
            $comments->where('report_id', $_GET['report_id']);
        }
        if (isset($_GET['query'])) {
            $comments->where(function ($q) {
                $columns = Schema::getColumnListing('comments');
                $q->whereHas('user', function ($q) {
                    $q->Where('full_name', 'LIKE', '%' . $_GET['query'] . '%');
                });
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
                }
            });
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query') {
                    continue;
                } else {
                    $sort = $value == 'true' ? 'desc' : 'asc';
                    $comments->orderBy($key,  $sort);
                }
            }
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($comments->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب التعليقات بنجاح', [], $res["model"], null, $res["count"]);
    }

    public function addComment(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'report_id' => 'required|exists:reports,id',
            'parent_id' => 'exists:comments,id',
            'body' => 'required'
        ], [
            'report_id.required' => 'يجب ادخال  تبليغ ',
            'report_id.exists' => 'يجب ادخال  تبليغ موجود ',
            'parent_id.exists' => 'يجب ان ادخال تعليق صحيح',
            'body.required' => 'يجب ادخال نص للتعليق'
        ]);

        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $report = Report::find($request['report_id']);
        if ($report->to_time != null) {
            if ($report->to_time < Carbon::now()) {
                return $this->send_response(200, 'انتهى وقت الرد على التعليق', [], []);
            }
        }
        $data = [];
        $data = [
            'user_id' => auth()->user()->id,
            'school_id' => auth()->user()->school->id,
            'body' => $request['body'],
            'report_id' => $request['report_id']
        ];


        if (array_key_exists('parent_id', $request)) {
            $re_comment = Comment::find($request['parent_id']);
            $user = User::find($re_comment->user_id);
            $data['parent_id'] = $request['parent_id'];
            $comment = Comment::Create($data);
            if (auth()->user()->id != $re_comment->user_id) {
                $notify =  Notification::Create([
                    "title" => 'تم اضافة رد على تعليقك',
                    "body"  => $request['body'],
                    "target_id" => $comment->id,
                    "from"  => auth()->user()->id,
                    "type"  => 0,
                    "school_id" => auth()->user()->school->id
                ]);
                $notify->users()->attach($user);
                broadcast(new AuthNotification($notify, $re_comment->user_id, "add"));
                foreach ($user->firebaseTokens as $token) {
                    try {
                        $this->send_notification_firebase("تم اضافة رد على تعليقك", $request['body'], $token->token);
                    } catch (Exception $th) {
                        $token->delete();
                    }
                }
            }
        } else {
            $comment = Comment::Create($data);
            if (auth()->user()->id != $report->issuer_id) {
                $user = User::find($report->issuer_id);
                $notify =  Notification::Create([
                    "title" =>  'تم اضافة تعليق على تبليغ خاص بك',
                    "body"  => $request['body'],
                    "target_id" => $comment->id,
                    "from"  => auth()->user()->id,
                    "type"  => 0,
                    "school_id" => auth()->user()->school->id
                ]);
                $notify->users()->attach($user);
                broadcast(new AuthNotification($notify, $report->issuer_id, "add"));
                foreach ($user->firebaseTokens as $token) {
                    try {
                        $this->send_notification_firebase("تم اضافة تعليق على تبليغ خاص بك", $request['body'], $token->token);
                    } catch (Exception $th) {
                        $token->delete();
                    }
                }
            }
        }
        if (array_key_exists('images', $request)) {
            foreach ($request['images'] as $image) {
                Image::create([
                    'image' => $this->uploadPicture($image, '/images/'),
                    'comment_id' => $comment->id,
                    'school_id' => auth()->user()->School->id
                ]);
            }
        }
        broadcast(new CommentSocket($comment, $report, "add"));



        return $this->send_response(200, 'تم اضافة تعليق', [], Comment::with('user', 'parent', "parent.user")->find($comment->id));
    }


    public function deleteComment(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'comment_id' => 'required|exists:comments,id'
        ], [
            'comment_id.required' => 'يجب اختيار تعليق',
            'comment_id.exists' => 'يجب ادخال تعليق موجود',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }

        $comment =  Comment::find($request['comment_id']);
        broadcast(new CommentSocket($comment, $comment->report, "delete"));
        foreach ($comment->notifications as $notify) {
            broadcast(new AuthNotification($notify, $notify->users[0]->id, "delete"));
        }


        if ($comment->user_id == auth()->user()->id || auth()->user()->user_type == 1) {
            $comment->delete();
            return $this->send_response(200, 'تم حذف تعليق بنجاح', [], []);
        } else {
            return $this->send_response(401, 'لايمكنك حذف تعليق غير خاص بك', [], []);
        }
    }
}