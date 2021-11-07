<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Report;
use App\Traits\SendResponse;
use App\Traits\UploadImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    use SendResponse, UploadImage;
    public function addReport(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'type' => 'required',
            'material_id' =>  $request['type'] == 3 || $request['type'] == 4 ? 'required|exists:materials,id' : '',
            'user_id' => $request['type'] == 0 ? 'required|exists:users,id' : '',
            'class_id' => $request['type'] == 3 || $request['type'] == 4 ? 'required|exists:stages,id' : '',
            'body' => 'required',
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
            'body' => $request['body'],
            'type' => $request['type'],
            'issuer_id' => auth()->user()->id,
            'school_id' => auth()->user()->School->id
        ];
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
        $report = Report::create($data);
        if (array_key_exists('images', $request)) {
            foreach ($request['images'] as $image) {
                Image::create([
                    'image' => $this->uploadPicture($image, '/images/'),
                    'report_id' => $report->id,
                    'school_id' => auth()->user()->School->id
                ]);
            }
        }

        return $this->send_response(200, 'تم اضافة تبليغ بنجاح', [], Report::with('user', 'issuer', 'images', 'stage', 'material')->find($report->id));
    }
}