<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\ProfileController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/auth/request-otp', [AuthController::class, 'sendOtp']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/auth/user',[AuthController::class,'user'])->middleware('auth:sanctum');
Route::post('/auth/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');

Route::post('/profile/change-password',[ProfileController::class,'change_password'])->middleware('auth:sanctum');
Route::post('/profile/update-profile',[ProfileController::class,'update_profile'])->middleware('auth:sanctum');

Route::get('/categories', [CategoryController::class, 'allCategories']);
Route::get('/books', [BookController::class, 'getBooks']);
Route::get('/courses', [CourseController::class, 'getCourses']);
Route::get('/course-chapters/{id}', [CourseController::class, 'getCourseChapters']);
Route::group(['prefix' => 'chat'], function () {
    Route::get('/messages/{user_id}', [ChatController::class, 'getMessages']);
    Route::post('send', [ChatController::class, 'sendMessage']);
    Route::delete('/messages/{id}', [ChatController::class, 'deleteMessage']);
});
