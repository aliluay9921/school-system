<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Image;
use App\Models\Report;
use App\Traits\Pagination;
use App\Traits\UploadImage;
use App\Models\Notification;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use App\Events\AbsentSockets;
use App\Events\ReportClassSockets;
use App\Events\ReportGeneralSockets;
use App\Models\FirebaseToken;
use App\Traits\SendNotificationFirebase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    use SendResponse, UploadImage, Pagination, SendNotificationFirebase;

    public function send_notification($report, $notification_type)
    {
        if ($report->type == 0) {
            $user = User::find($report->user_id);
            if ($notification_type != "delete") {
                foreach ($user->firebaseTokens as $token) {
                    $this->send_notification_firebase("تبليغ غياب", $report->body, $token->token);
                }
            }
            broadcast(new AbsentSockets($report, $user, $notification_type));
        } elseif ($report->type == 1) {
            $school_id = auth()->user()->school->id;
            $tokens = FirebaseToken::whereHas("user", function ($q) use ($school_id) {
                $q->where("school_id", $school_id);
            })->get();
            if ($notification_type != "delete") {
                foreach ($tokens as $token) {
                    $this->send_notification_firebase("تبليغ عام", $report->body, $token->token);
                }
            }
            broadcast(new ReportGeneralSockets($report, $school_id, $notification_type));
        } elseif ($report->type == 2) {
            $user = User::find($report->user_id);
            if ($notification_type != "delete") {
                foreach ($user->firebaseTokens as $token) {
                    $this->send_notification_firebase("تبليغ خاص", $report->body, $token->token);
                }
            }
            broadcast(new AbsentSockets($report, $user, $notification_type));
        } elseif ($report->type == 3) {
            $tokens = FirebaseToken::whereHas("user", function ($q) use ($report) {
                $q->where("class_id", $report->class_id);
            })->get();
            if ($notification_type != "delete") {
                foreach ($tokens as $token) {
                    $this->send_notification_firebase("تبليغ امتحان", $report->body, $token->token);
                }
            }
            broadcast(new ReportClassSockets($report, $report->class_id, $notification_type));
        } else {
            $tokens = FirebaseToken::whereHas("user", function ($q) use ($report) {
                $q->where("class_id", $report->class_id);
            })->get();
            if ($notification_type != "delete") {
                foreach ($tokens as $token) {
                    $this->send_notification_firebase("تبليغ واجب", $report->body, $token->token);
                }
            }
            broadcast(new ReportClassSockets($report, $report->class_id, $notification_type));
        }
    }


    public function getReports()
    {
        if (auth()->user()->user_type == 2) {
            $reports = Report::where('school_id', auth()->user()->School->id)->where(function ($q) {
                $q->orWhere('issuer_id', auth()->user()->id)->orWhere('type', 1);
            });
            //    الية عمل الفلتر ب اكثر من بارميتر 
            if (isset($_GET)) {
                foreach ($_GET as $key => $value) {
                    if ($key == 'skip' || $key == 'limit') {
                        continue;
                    } else {
                        $reports->where($key, $value);
                    }
                }
            }
        } elseif (auth()->user()->user_type == 3) {
            $reports = Report::with('user', 'issuer', 'images', 'stage', 'material')->where('school_id', auth()->user()->School->id)->where(function ($q) {
                $q->orWhere('type', 1)->orWhere('user_id', auth()->user()->id)->orWhere('class_id', auth()->user()->class_id);
            });
            if (isset($_GET)) {
                foreach ($_GET as $key => $value) {
                    if ($key == 'skip' || $key == 'limit') {
                        continue;
                    } else {
                        $reports->where($key, $value);
                    }
                }
            }
        } else {
            $reports = Report::with('user', 'issuer', 'images', 'stage', 'material')->where('school_id', auth()->user()->School->id);
            if (isset($_GET['filter'])) {
                $filter = json_decode($_GET['filter']); //لان اوبجكت لازم تفتحه
                $reports->where($filter->name, $filter->value);
            }
            if (isset($_GET['query'])) {
                $reports->where(function ($q) {
                    $columns = Schema::getColumnListing('reports');
                    $q->whereHas('issuer', function ($q) {
                        $q->Where('full_name', 'LIKE', '%' . $_GET['query'] . '%');
                    })->orWhereHas('user', function ($q) {
                        $q->Where('full_name', 'LIKE', '%' . $_GET['query'] . '%');
                    })->orWhereHas('stage', function ($q) {
                        $q->Where('name', 'LIKE', '%' . $_GET['query'] . '%');
                    })->orWhereHas('material', function ($q) {
                        $q->Where('name', 'LIKE', '%' . $_GET['query'] . '%');
                    });
                    foreach ($columns as $column) {
                        $q->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
                    }
                });
            }
            if (isset($_GET)) {
                foreach ($_GET as $key => $value) {
                    if ($key == 'skip' || $key == 'limit' || $key == 'query' || $key == 'filter') {
                        continue;
                    } else {
                        $sort = $value == 'true' ? 'desc' : 'asc';
                        $reports->orderBy($key,  $sort);
                    }
                }
            }
        }

        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($reports->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب التبليغات بنجاح', [], $res["model"], null, $res["count"]);
    }


    public function addReport(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'type' => 'required',
            'material_id' =>  $request['type'] == 3 || $request['type'] == 4 ? 'required|exists:materials,id' : '',
            'user_id' => $request['type'] == 0 ? 'required|exists:users,id' : '',
            'class_id' => $request['type'] == 3 || $request['type'] == 4 ? 'required|exists:stages,id' : '',
            'body' => $request['type'] == 0 || $request['type'] == 1 || $request['type'] == 2 ? 'required' : '',
            'to_time' => 'required_with:from_time|after:from_time',
            'from_time' => 'required_with:to_time'
        ], [
            'material_id.required' => 'يجب ادخال مادة',
            'user_id.required' => 'يجب ادخال  طالب  ',
            'class_id.required' => 'يجب ادخال صف  ',
            'material_id.exists' => 'يجب ادخال مادة موجودة',
            'user_id.exists' => 'يجب ادخال  طالب موجود ',
            'class_id.exists' => 'يجب ادخال صف موجود ',
            'body.required' => 'يجب ادخال النص المراد ارساله',
            'type.required' => 'يجب ادخال نوع التبليغ',
            'to_time.required_with' => 'يجب ادخال الوقت',
            'to_time.after' => 'يجب ان يكون نهاية الوقت مناسب',
            'from_time.required_with' => 'يجب ادخال الوقت',

        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $data = [];
        $data = [
            'type' => $request['type'],
            'issuer_id' => auth()->user()->id,
            'school_id' => auth()->user()->School->id
        ];
        if (array_key_exists('body', $request)) {
            $data['body'] = $request['body'];
        }
        if (array_key_exists('material_id', $request)) {
            $data['material_id'] = $request['material_id'];
        }
        if (array_key_exists('class_id', $request)) {
            $data['class_id'] = $request['class_id'];
        }
        if (array_key_exists('user_id', $request)) {
            $data['user_id'] = $request['user_id'];
        }
        if (array_key_exists('to_time', $request)) {
            $data['to_time'] = $request['to_time'];
        }
        if (array_key_exists('from_time', $request)) {
            $data['from_time'] = $request['from_time'];
        }
        $new_report = Report::create($data);
        if (array_key_exists('images', $request)) {
            foreach ($request['images'] as $image) {
                Image::create([
                    'image' => $this->uploadPicture($image, '/images/'),
                    'report_id' => $new_report->id,
                    'school_id' => auth()->user()->School->id
                ]);
            }
        }
        $report = Report::with('user', 'issuer', 'images', 'stage', 'material')->find($new_report->id);
        $this->send_notification($report, "add");

        return $this->send_response(200, 'تم اضافة تبليغ بنجاح', [], Report::with('user', 'issuer', 'images', 'stage', 'material')->find($report->id));
    }

    public function editReport(Request $request)
    {
        $request = $request->json()->all();

        $validator = Validator::make($request, [
            "report_id" => 'required|exists:reports,id',
            "type"  => 'required',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $report = Report::find($request['report_id']);
        if ($request['type'] == 0) {
            $report->update([
                "body" => $request['body'],
            ]);
            $this->send_notification(Report::find($request['report_id']), "edit");
            return $this->send_response(200, 'تم التعديل على التبليغ', [], []);
        } elseif ($request['type'] == 1) {
            $images = [];
            if ($report->images->count() == 4) {
                return $this->send_response(401, 'لايمكنك اضافة اكثر من 4 صور', [], []);
            }
            foreach ($request['images'] as $image) {
                $new_image =  Image::create([
                    'image' => $this->uploadPicture($image, '/images/'),
                    'report_id' => $report->id,
                    'school_id' => auth()->user()->School->id
                ]);
                $images[] = $new_image;
            }
            $this->send_notification(Report::find($request['report_id']), "edit");
            return $this->send_response(200, 'تم التعديل على التبليغ', [], $images);
        } else {
            Image::find($request['image_id'])->delete();
            $this->send_notification(Report::find($request['report_id']), "edit");
            return $this->send_response(200, 'تم التعديل على التبليغ', [], []);
        }
    }

    public function deleteReport(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'report_id' => 'required|exists:reports,id'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $report = Report::find($request['report_id']);
        $this->send_notification($report, "delete");
        $report->delete();
        return $this->send_response(200, 'تم حذف التبليغ', [], []);
    }
}