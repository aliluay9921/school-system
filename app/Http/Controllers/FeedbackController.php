<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    use SendResponse, Pagination;

    public function getFeedbacks()
    {
        $feedbacks = Feedback::with('user')->where('school_id', auth()->user()->School->id);
        if (isset($_GET['query'])) {
            $feedbacks->where(function ($q) {
                $columns = Schema::getColumnListing('feedbacks');
                $q->whereHas('user', function ($q) {
                    $q->Where('full_name', 'LIKE', '%' . $_GET['query'] . '%')->orWhere('phone_number', 'LIKE', '%' . $_GET['query'] . '%');
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
                    $feedbacks->orderBy($key,  $sort);
                }
            }
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($feedbacks,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب ردود بنجاح', [], $res["model"], null, $res["count"]);
    }


    public function addFeedback(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'text' => 'required',
        ], [
            'text.required' => 'يجب ادخال الشكوى',


        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }

        $feedback = Feedback::create([
            'school_id' => auth()->user()->school->id,
            'text' => $request['text'],
            'user_id' => auth()->user()->id,
        ]);
        return $this->send_response(200, 'تم ارسال الشكوى بنجاح', [], Feedback::with('user')->find($feedback->id));
    }
}