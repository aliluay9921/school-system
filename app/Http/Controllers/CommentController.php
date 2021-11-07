<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Report;
use App\Traits\SendResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    use SendResponse;
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
            $data['parent_id'] = $request['parent_id'];
        }
        $comment = Comment::Create($data);
        return $this->send_response(200, 'تم اضافة تعليق', [], Comment::with('user')->find($comment));
    }
}