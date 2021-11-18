<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DailyMaterialController;
use App\Http\Controllers\DegreeController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialStageTeacherController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Broadcast::routes(['middleware' => ['auth:api']]);
route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {
    route::get('get_users', [UserController::class, 'getUsers']);
    route::get('auth_info', [AuthController::class, 'authInfo']);
    route::get('get_material_stage_teacher', [MaterialStageTeacherController::class, 'getMaterialStageTeacher']);
    route::get('get_materials', [MaterialController::class, 'getMaterials']);
    route::get('get_stages', [StageController::class, 'getStages']);
    route::get('get_semesters', [SemesterController::class, 'getSemesters']);
    route::get('get_degrees', [DegreeController::class, 'getDegrees']);
    route::get('get_feedbacks', [FeedbackController::class, 'getFeedbacks']);
    route::get('get_exams', [ExamController::class, 'getExams']);
    route::get('get_payments', [PaymentsController::class, 'getPayments']);
    route::get('get_reports', [ReportController::class, 'getReports']);
    route::get('get_schools', [SchoolController::class, 'getSchool']);
    route::get('get_daily_materials', [DailyMaterialController::class, 'getDailyMaterials']);
    route::get('get_comments', [CommentController::class, 'getCommtns']);
    route::get("get_notification", [NotificationController::class, "getNotification"]);
    route::post('add_feedback', [FeedbackController::class, 'addFeedback']);
    route::post('add_comment', [CommentController::class, 'addComment']);
    route::post('add_report', [ReportController::class, 'addReport']);


    route::delete('delete_comment', [CommentController::class, 'deleteComment']);


    route::middleware('admin')->group(function () {
        route::post('add_school', [SchoolController::class, 'add_school']);
    });
    route::middleware('manager')->group(function () {

        route::post("show_pass", [UserController::class, 'showPass']);

        route::post('add_user', [UserController::class, 'addUser']);
        route::post('add_stage', [StageController::class, 'addStage']);
        route::post('add_semester', [SemesterController::class, 'addSemester']);
        route::post('add_material', [MaterialController::class, 'addMaterial']);
        route::post('add_daily_materials', [DailyMaterialController::class, 'addDailyMaterial']);
        route::post('add_payment', [PaymentsController::class, 'addPayment']);
        route::post('add_material_stage_teacher', [MaterialStageTeacherController::class, 'addMaterialStageTeacher']);
        route::post('add_degree', [DegreeController::class, 'addDegree']);

        route::put("edit_daily_material", [DailyMaterialController::class, "editDailyMaterial"]);
        route::put("edit_exam", [ExamController::class, "editExam"]);

        route::delete('delete_user', [UserController::class, 'deleteUser']);
        route::delete('delete_daily_material', [DailyMaterialController::class, 'deleteDailyMaterial']);
        route::delete('delete_exam', [ExamController::class, 'deleteExam']);
        route::delete('delete_semester', [SemesterController::class, 'deleteSemester']);
        route::delete('delete_material', [MaterialController::class, 'deleteMaterial']);
        route::delete("delete_report", [ReportController::class, "deleteReport"]);
        route::delete("delete_material_stage_teacher", [MaterialStageTeacherController::class, 'deleteMaterialStageTeacher']);
    });
    route::middleware('teacher')->group(function () {
        route::post('add_exam', [ExamController::class, 'addExam']);
        route::put('edit_report', [ReportController::class, "editReport"]);
    });
});
