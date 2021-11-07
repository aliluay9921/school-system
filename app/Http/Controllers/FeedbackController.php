<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    use SendResponse;
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
