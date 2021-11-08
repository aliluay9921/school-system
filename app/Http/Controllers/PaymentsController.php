<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentsController extends Controller
{
    use SendResponse, Pagination;

    public function getPayments()
    {
        $payments = Payment::with('user')->where('school_id', auth()->user()->School->id);
        $payments->whereHas('user', function ($q) {
            $q->where('user_type', $_GET['user_type']);
        });
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($payments,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب المدفوعات بنجاح', [], $res["model"], null, $res["count"]);
    }


    public function addPayment(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'pay_date' => 'required',
            'value' => 'required',
            'user_id' => 'required|exists:users,id'
        ], [
            'pay_date.required' => 'يجب ادخال يوم الدفع',
            'value.required' => 'يجب ادخال قيمة الدفعة',
            'user_id.required' => 'يجب ادخال المستحدم'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }



        $payment = Payment::create([
            'school_id' => auth()->user()->school->id,
            'pay_date' => $request['pay_date'],
            'value' => $request['value'],
            'user_id' => $request['user_id'],
        ]);

        $user = User::find($request['user_id']);

        if ($user->stage->fee == $user->payments->sum('value')) {
            $user->update([
                'paid' => false
            ]);
        }
        return $this->send_response(200, 'تم اضافة دفع  بنجاح', [], Payment::with('user')->find($payment->id));
    }
}
