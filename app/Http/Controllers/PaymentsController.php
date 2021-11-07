<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentsController extends Controller
{
    use SendResponse;
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
        return $this->send_response(200, 'تم اضافة دفع  بنجاح', [], Payment::with('user')->find($payment->id));
    }
}