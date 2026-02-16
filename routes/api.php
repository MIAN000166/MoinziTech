<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|

| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->group( function () {

//Admin auth routes
    Route::post('admin/logout',[\App\Http\Controllers\Admin\LoginController::class,'logout']);
    Route::get('admin/get/hospital/reports', [\App\Http\Controllers\Admin\ReportController::class,'index']);
    Route::post('admin/update/report/status',[\App\Http\Controllers\Admin\ReportController::class,'update_status']);
    Route::post('admin/forward/case/radiologist',[\App\Http\Controllers\Admin\ReportController::class,'forward_case']);
    Route::post('admin/update/report',[\App\Http\Controllers\Admin\ReportController::class,'update']);
    Route::delete('admin/del/report/{id}',[\App\Http\Controllers\Admin\ReportController::class,'destroy']);
    Route::get('admin/get/single/report/{id}',[\App\Http\Controllers\Admin\ReportController::class,'get_report']);
    Route::get('admin/get/approved/reports',[\App\Http\Controllers\Admin\ReportController::class,'all_approved_reports']);
    Route::get('admin/get/pending/reports',[\App\Http\Controllers\Admin\ReportController::class,'all_pending_reports']);
    Route::get('admin/get/assigned/case/reports',[\App\Http\Controllers\Admin\ReportController::class,'all_forward_case_reports']);
    Route::get('admin/get/all/users',[\App\Http\Controllers\Admin\UserController::class,'index']);
    Route::get('admin/get/user/{id}',[\App\Http\Controllers\Admin\UserController::class,'single_user']);
//    Route::get('admin/get/user/{id}',[\App\Http\Controllers\Admin\UserController::class,'single_user']);
    Route::post('admin/update/user',[\App\Http\Controllers\Admin\UserController::class,'update']);
    Route::delete('admin/del/user/{id}',[\App\Http\Controllers\Admin\UserController::class,'del_user']);
    Route::post('admin/change/user/status',[\App\Http\Controllers\Admin\UserController::class,'change_user_status']);
    Route::post('admin/add/report/comment',[\App\Http\Controllers\Admin\ReportController::class,'add_comment']);
    Route::post('admin/get/checked/reports',[\App\Http\Controllers\Admin\ReportController::class,'get_checked_reports']);
    Route::get('admin/checked/reports',[\App\Http\Controllers\Admin\ReportController::class,'checked_reports']);
    Route::post('admin/update/user/password',[\App\Http\Controllers\Admin\UserController::class,'update_password']);

    Route::post('admin/case/taking/self',[\App\Http\Controllers\Admin\ReportController::class,'self_taking']);
    Route::get('admin/get/all/radiologists',[\App\Http\Controllers\Admin\UserController::class,'all_radiologists']);
    Route::post('admin/radiologist/tracking',[\App\Http\Controllers\Admin\TrackController::class,'tracking']);
    Route::post('admin/hospital/tracking',[\App\Http\Controllers\Admin\TrackController::class,'hospital_tracking']);

    Route::get('admin/track/count',[\App\Http\Controllers\Admin\TrackController::class,'track_count']);
    Route::post('admin/auto/case/assigning',[\App\Http\Controllers\Admin\ReportController::class,'auto_assign_case']);
    Route::get('admin/get/toggle/status',[\App\Http\Controllers\Admin\TrackController::class,'toggle_status']);
    Route::post('admin/add/excel/file',[\App\Http\Controllers\Admin\ReportController::class,'add_extra_file']);

//Admin auth routes end
//    abc

//Hospital Auth routes
    Route::post('hospital/logout',[\App\Http\Controllers\Hospital\LoginController::class,'logout']);
    Route::post('hospital/add/patient/report',[\App\Http\Controllers\Hospital\ReportController::class,'store']);
    Route::get('hospital/reports', [\App\Http\Controllers\Hospital\ReportController::class,'index']);
    Route::get('hospital/get/single/report/{id}', [\App\Http\Controllers\Hospital\ReportController::class,'single_report']);
    Route::post('hospital/update/report', [\App\Http\Controllers\Hospital\ReportController::class,'update']);
    Route::get('hospital/get/all/pending/reports', [\App\Http\Controllers\Hospital\ReportController::class,'pending_reports']);
    Route::get('hospital/get/all/approved/reports', [\App\Http\Controllers\Hospital\ReportController::class,'approved_reports']);
    Route::delete('hospital/del/report/{id}', [\App\Http\Controllers\Hospital\ReportController::class,'destroy']);
    Route::get('hospital/get/all/radiologists',[\App\Http\Controllers\Hospital\RadiologistController::class,'index']);
    Route::get('hospital/get/radiologist/{id}',[\App\Http\Controllers\Hospital\RadiologistController::class,'single_radiologist']);
    Route::delete('hospital/del/radiologist/{id}',[\App\Http\Controllers\Hospital\RadiologistController::class,'del_radiologist']);
    Route::post('hospital/change/radiologist/status',[\App\Http\Controllers\Hospital\RadiologistController::class,'change_radiologist_status']);
    Route::get('hospital/checked/reports',[\App\Http\Controllers\Hospital\ReportController::class,'checked_reports']);
    Route::post('hospital/get/checked/reports',[\App\Http\Controllers\Hospital\RadiologistController::class,'get_checked_reports']);
    Route::get('hospital/track/count',[\App\Http\Controllers\Hospital\TrackController::class,'track_count']);
//Hospital AUth routes end

//Radiologist auth routes
    Route::post('radiologist/logout',[\App\Http\Controllers\Radiologist\LoginController::class,'logout']);
    Route::get('radiologist/get/all/reports',[\App\Http\Controllers\Radiologist\ReportController::class,'index']);
    Route::post('radiologist/update/report/status',[\App\Http\Controllers\Radiologist\ReportController::class,'update_status']);
    Route::get('radiologist/get/single/report/{id}',[\App\Http\Controllers\Radiologist\ReportController::class,'get_report']);
    Route::get('radiologist/get/checked/reports',[\App\Http\Controllers\Radiologist\ReportController::class,'completed_reports']);
    Route::get('radiologist/get/pending/reports',[\App\Http\Controllers\Radiologist\ReportController::class,'pending_reports']);
    Route::get('radiologist/report/track',[\App\Http\Controllers\Radiologist\ReportController::class,'track']);
    Route::post('radiologist/filter/checked/reports',[\App\Http\Controllers\Radiologist\ReportController::class,'filter_reports']);
    Route::post('radiologist/add/extra/file',[\App\Http\Controllers\Radiologist\ReportController::class,'add_extra_file']);
//Radiologist auth routes end

});
Route::post('contact/us',[\App\Http\Controllers\ContactUsController::class,'store']);

//Admin  routes
Route::post('admin/login',[\App\Http\Controllers\Admin\LoginController::class,'login']);

//Admin  routes end

//Hospital  routes
Route::post('hospital/login',[\App\Http\Controllers\Hospital\LoginController::class,'login']);
Route::post('hospital/registration', [\App\Http\Controllers\Hospital\RegistrationController::class, 'register']);
Route::post('hospital/verification', [\App\Http\Controllers\Hospital\RegistrationController::class, 'verify']);
//Hospital  routes end

//Radiologist  routes
Route::post('radiologist/login',[\App\Http\Controllers\Radiologist\LoginController::class,'login']);
Route::post('radiologist/registration', [\App\Http\Controllers\Radiologist\RegistrationController::class, 'register']);
Route::post('radiologist/verification', [\App\Http\Controllers\Radiologist\RegistrationController::class, 'verify']);
//Radiologist  routes end

Route::get('file/download/{filename}',[\App\Http\Controllers\FileController::class,'download']);

Route::get('download/internal/image/{url}',[\App\Http\Controllers\Controller::class,'download']);
