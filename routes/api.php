<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DailyMaterialController;
use App\Http\Controllers\DegreeController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialStageTeacherController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {
    route::post('add_feedback', [FeedbackController::class, 'addFeedback']);
    route::post('add_comment', [CommentController::class, 'addComment']);
    route::post('add_report', [ReportController::class, 'addReport']);
    route::get('auth_info', [AuthController::class, 'authInfo']);

    route::middleware('admin')->group(function () {
        route::post('add_school', [SchoolController::class, 'add_school']);
    });
    route::middleware('manager')->group(function () {
        route::post('add_user', [UserController::class, 'addUser']);

        route::get('get_users', [UserController::class, 'getUsers']);
        route::post('add_stage', [StageController::class, 'addStage']);
        route::post('add_semester', [SemesterController::class, 'addSemester']);
        route::post('add_material', [MaterialController::class, 'addMaterial']);
        route::post('add_daily_materials', [DailyMaterialController::class, 'addDailyMaterial']);
        route::post('add_payment', [PaymentsController::class, 'addPayment']);
        route::post('add_material_stage_teacher', [MaterialStageTeacherController::class, 'addMaterialStageTeacher']);
        route::post('add_degree', [DegreeController::class, 'addDegree']);
    });
    route::middleware('teacher')->group(function () {
        route::post('add_exam', [ExamController::class, 'addExam']);
    });
});