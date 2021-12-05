<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Payment;
use App\Traits\Pagination;
use App\Models\Notification;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use App\Models\FirebaseToken;
use Illuminate\Support\Facades\Schema;
use App\Traits\SendNotificationFirebase;
use Illuminate\Support\Facades\Validator;

class PaymentsController extends Controller
{
    use SendResponse, Pagination, SendNotificationFirebase;

    public function getPayments()
    {
        $payments = Payment::with('user')->where('school_id', auth()->user()->School->id);
        if (isset($_GET['user_type'])) {
            $payments->whereHas('user', function ($q) {
                $q->where('user_type', $_GET['user_type']);
            });
        }
        if (isset($_GET['query'])) {
            $columns = Schema::getColumnListing('payments');
            $payments->whereHas("user", function ($q) {
                $q->where('full_name', 'LIKE', '%' . $_GET['query'] . '%');
            });
            foreach ($columns as $column) {
                $payments->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
            }
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query') {
                    continue;
                } else {
                    $sort = $value == 'true' ? 'desc' : 'asc';
                    $payments->orderBy($key,  $sort);
                }
            }
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($payments->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
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
        $user = User::find($request['user_id']);
        if ($user->user_type == 2) {
            if ($user->salary < $request["value"]) {
                return $this->send_response("401", "لايمكنك اضافة قيمة اكبر من قيمة الراتب لهذا المدرس", [], []);
            }
        }
        $payment = Payment::create([
            'school_id' => auth()->user()->school->id,
            'pay_date' => $request['pay_date'],
            'value' => $request['value'],
            'user_id' => $request['user_id'],
        ]);

        if ($user->user_type == 3) {
            if ($user->stage->fee == $user->payments->sum('value')) {
                $user->update([
                    'paid' => false
                ]);
            }
            $notification = Notification::create([
                'title' => 'تبليغ القسط',
                'body'  => 'تم استلام مبلغ القسط من قبل ادارة المدرسة وقيمته' . $request['value'],
                'from'  => auth()->user()->id,
                'type'  => 0
            ]);
            foreach ($user->firebaseTokens as $token) {

                try {
                    $this->send_notification_firebase('تم استلام مبلغ القسط', $notification->body, $token->token);
                } catch (Exception $th) {
                    FirebaseToken::find($token->id)->delete();
                }
            }
        } else {
            $notification = Notification::create([
                'title' => 'تبليغ استلام راتب',
                'body'  => 'تم تسليم الراتب الخاص بالتدريسي ' . $user->full_name . ' وقيمته' . $request['value'],
                'from'  => auth()->user()->id,
                'type'  => 0
            ]);
            foreach ($user->firebaseTokens as $token) {
                try {
                    $this->send_notification_firebase('تبليغ استلام راتب', $notification->body, $token->token);
                } catch (Exception $th) {
                    FirebaseToken::find($token->id)->delete();
                }
            }
        }
        $notification->users()->attach($user);
        return $this->send_response(200, 'تم اضافة دفع  بنجاح', [], Payment::with('user')->find($payment->id));
    }
}