<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    use SendResponse, Pagination;

    public function getExams()
    {
        if (isset($_GET['class_id'])) {
            $exams = Exam::with('material', 'stage')->where('school_id', auth()->user()->School->id)->where('class_id', $_GET['class_id'])->orderBy('date', "ASC")->get();
            return $this->send_response(200, 'تم جلب امتحانات الصف ', [], $exams);
        }
        $exams = Exam::with('material', 'stage')->where('school_id', auth()->user()->School->id)->orderBy('date', "ASC");
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($exams,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب الامتحانات بنجاح', [], $res["model"], null, $res["count"]);
    }



    public function addExam(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'class_id' => 'required|exists:stages,id',
            'lesson_number' => 'required',
            'material_id' => 'required|exists:materials,id',
            'day' => 'required',
            'date' => 'required|date|after:today'
        ], [
            'class_id.required' => 'يجب ادخال الصف',
            'class_id.exists' => 'يجب ادخال صف موجود',
            'material_id.required' => 'يجب ادخال المادة',
            'material_id.exists' => 'يجب ادخال مادة موجودة',
            'day.required' => 'يجب ادخال اليوم',
            'lesson_number.required' => 'يجب ادخال عدد دروس الامتحان',
            'date.required' => 'يجب ادخال التاريخ',
            'date.after' => 'يجب ادخال تأريخ الامتحان من بعد تأريخ اليوم'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }

        $exam = Exam::create([
            'school_id' => auth()->user()->school->id,
            'class_id' => $request['class_id'],
            'material_id' => $request['material_id'],
            'lesson_number' => $request['lesson_number'],
            'day' => $request['day'],
            'date' => $request['date']
        ]);
        return $this->send_response(200, 'تم اضافة امتحان  بنجاح', [], Exam::find($exam->id));
    }
    public function deleteExam(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'exam_id' => 'required|exists:exams,id'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        Exam::find($request['exam_id'])->delete();
        return $this->send_response(200, 'تم حذف الامتحان', [], []);
    }
}
