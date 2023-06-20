<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ApiConfigController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiUserController;
use App\Http\Controllers\Api\ApiCategoryController;
use App\Http\Controllers\Api\ApiPermissionController;
use App\Http\Controllers\Api\ApiLogController;
use App\Http\Controllers\Api\ApiReportController;
use App\Http\Controllers\Api\ApiStudentController;
use App\Http\Controllers\Api\ApiTeacherController;
use App\Http\Controllers\Api\ApiReportValueController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Public
Route::get('/config/list', [ApiConfigController::class, 'index'])
    ->name('api.config.list');


// Auth
Route::post('/login', [ApiAuthController::class, 'login'])
    ->name('api.auth.login');
Route::get('/logout', [ApiAuthController::class, 'logout'])
    ->name('api.auth.logout');

Route::middleware('auth:api')->group(function() {
    Route::get('/profile', [ApiUserController::class, 'profile'])
        ->name('api.profile');

    Route::post('/config/create', [ApiConfigController::class, 'store'])
        ->name('api.config.create');

    Route::get('/log/list', [ApiLogController::class, 'index'])
        ->name('api.log.list');

    Route::get('/user/list', [ApiUserController::class, 'index'])
        ->name('api.user.list');
    Route::post('/user/create', [ApiUserController::class, 'store'])
        ->name('api.user.create');
    Route::get('/user/detail/{id}', [ApiUserController::class, 'show'])
        ->name('api.user.detail');
    Route::delete('/user/{id}/{permanent?}', [ApiUserController::class, 'destroy'])
        ->name('api.user.delete');
    Route::get('/user/restore/{id}', [ApiUserController::class, 'restore'])
        ->name('api.user.restore');

    Route::get('/category/list', [ApiCategoryController::class, 'index'])
        ->name('api.category.list');
    Route::post('/category/create', [ApiCategoryController::class, 'store'])
    ->name('api.category.create');
    Route::get('/category/detail/{id}', [ApiCategoryController::class, 'show'])
        ->name('api.category.detail');
    Route::delete('/category/{id}/{permanent?}', [ApiCategoryController::class, 'destroy']);

    Route::get('/permission-group/list', [ApiPermissionController::class, 'index'])
        ->name('api.permission-group.list');
    Route::post('/permission-group/create', [ApiPermissionController::class, 'store'])
        ->name('api.permission-group.create');
    Route::get('/permission-group/detail/{id}', [ApiPermissionController::class, 'show'])
        ->name('api.permission-group.detail');
    Route::delete('/permission-group/{id}', [ApiPermissionController::class, 'destroy'])
        ->name('api.permission-group.delete');
    Route::get('/permission-group/permission/{id}', [ApiPermissionController::class, 'permission'])
        ->name('api.permission-group.permission');
    Route::get('/permission-group/export', [ApiPermissionController::class, 'export'])
        ->name('api.permission-group.export');
    Route::post('/permission-group/import', [ApiPermissionController::class, 'import'])
        ->name('api.permission-group.import');

    Route::get('/student/list', [ApiStudentController::class, 'index'])
        ->name('api.student.list');
    Route::post('/student/create', [ApiStudentController::class, 'store'])
        ->name('api.student.create');
    Route::get('/student/detail/{id}', [ApiStudentController::class, 'show'])
        ->name('api.student.detail');
    Route::delete('/student/{id}/{permanent?}', [ApiStudentController::class, 'destroy'])
        ->name('api.student.delete');
    Route::get('/student/restore/{id}', [ApiStudentController::class, 'restore'])
        ->name('api.student.restore');
    Route::get('/student/export', [ApiStudentController::class, 'export'])
        ->name('api.student.export');
    Route::post('/student/import', [ApiStudentController::class, 'import'])
        ->name('api.student.import');

    Route::get('/teacher/list', [ApiTeacherController::class, 'index'])
        ->name('api.teacher.list');
    Route::post('/teacher/create', [ApiTeacherController::class, 'store'])
        ->name('api.teacher.create');
    Route::get('/teacher/detail/{id}', [ApiTeacherController::class, 'show'])
        ->name('api.teacher.detail');
    Route::delete('/teacher/{id}/{permanent?}', [ApiTeacherController::class, 'destroy'])
        ->name('api.teacher.delete');
    Route::get('/teacher/restore/{id}', [ApiTeacherController::class, 'restore'])
        ->name('api.teacher.restore');

    Route::get('/report-value/list', [ApiReportValueController::class, 'index'])
        ->name('api.report-value.list');
    Route::post('/report-value/create', [ApiReportValueController::class, 'store'])
        ->name('api.report-value.create');
    Route::get('/report-value/detail/{id}', [ApiReportValueController::class, 'show'])
        ->name('api.report-value.detail');
    Route::delete('/report-value/{id}', [ApiReportValueController::class, 'destroy'])
        ->name('api.report-value.delete');
    Route::get('/report-value/get-report-value', [ApiReportValueController::class, 'getReportValue'])
        ->name('api.report-value.get-report-value');

    Route::get('/report/report-value/list', [ApiReportController::class, 'reportValueIndex'])
        ->name('api.report.report-value.list');
});