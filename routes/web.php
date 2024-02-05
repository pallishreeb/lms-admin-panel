<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomForgotPasswordController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\AppConfigController;
use App\Http\Controllers\TransactionController;


Route::get('/admin/config', [AppConfigController::class, 'index'])->name('admin.config');
Route::post('/admin/update-notification-preference', [AppConfigController::class, 'updateNotificationPreference'])->name('admin.updateNotificationPreference');
Route::get('/admin/dashboard', [AppConfigController::class, 'dashboard'])->name('admin.dashboard');

//chat
Route::get('/user-messages', [ChatMessageController::class, 'index'])->name('user-messages');
Route::get('/user-chats/{user}', [ChatMessageController::class, 'showUserChats'])->name('user-chats');
Route::post('/user-chats/{user}/reply', [ChatMessageController::class, 'reply'])->name('user-chats.reply');
Route::delete('/delete-chat/{id}', [ChatMessageController::class, 'delete'])->name('delete-chat');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/admin/dashboard', function () {
//     return view('welcome');
// });

Route::redirect('/', '/admin/dashboard')->middleware('auth');

// Show Register/Create Form
Route::get('/register', [AuthController::class, 'create'])->middleware('guest');

// Create New User
Route::post('/register', [AuthController::class, 'store'])->name('store');
// Show Login Form
Route::get('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');

// Log In User
Route::post('/users/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
// Log User Out
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
// password reset

// Show forgot password form
Route::get('/forgot-password', [CustomForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');

// Handle forgot password form submission
Route::post('/forgot-password', [CustomForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Show reset password form
Route::get('/reset-password/{token}', [CustomForgotPasswordController::class, 'showResetForm'])->name('password.reset');

// Handle reset password form submission
Route::post('/reset-password', [CustomForgotPasswordController::class, 'reset'])->name('password.update');

Route::get('/verify-otp', [AuthController::class, 'showOtpVerificationForm'])->name('verify.otp.form');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp');
Route::get('/admin/users', [AuthController::class, 'index'])->name('admin.users.index')->middleware('auth');
Route::get('/users/{user}/edit', [AuthController::class, 'edit'])->name('users.edit')->middleware('auth');
Route::put('/users/{user}', [AuthController::class, 'update'])->name('users.update')->middleware('auth');

// Destroy
Route::get('/users/delete/{user}', [AuthController::class, 'deleteConfirmation'])->name('users.delete-confirmation')->middleware('auth');
Route::delete('/users/{user}', [AuthController::class, 'destroy'])->name('users.destroy')->middleware('auth');

// Route::resource('/admin/categories', CategoryController::class);
Route::resource('/admin/books', BookController::class)->middleware('auth');
Route::resource('/admin/courses', CourseController::class)->middleware('auth');

Route::get('chapters/create/{courseId}', [ChapterController::class, 'create'])->name('chapters.create')->middleware('auth');
Route::post('/courses/chapters', [ChapterController::class, 'store'])->name('chapters.store')->middleware('auth');
Route::get('/chapters/{chapter}/edit', [ChapterController::class, 'edit'])->name('chapters.edit')->middleware('auth');
Route::put('/chapters/{chapter}', [ChapterController::class, 'update'])->name('chapters.update')->middleware('auth');
Route::delete('/chapters/{chapter}', [ChapterController::class, 'destroy'])->name('chapters.destroy')->middleware('auth');
// Categories
Route::get('/admin/categories', [CategoryController::class, 'index'])->name('categories.index')->middleware('auth');
Route::get('/admin/categories/create', [CategoryController::class, 'create'])->name('categories.create')->middleware('auth');
Route::post('/admin/categories', [CategoryController::class, 'store'])->name('categories.store')->middleware('auth');
Route::get('/admin/categories/{category}', [CategoryController::class, 'show'])->name('categories.show')->middleware('auth');
Route::get('/admin/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit')->middleware('auth');
Route::put('/admin/categories/{category}', [CategoryController::class, 'update'])->name('categories.update')->middleware('auth');
Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy')->middleware('auth');
Route::get('/admin/categories/{category}', [CategoryController::class, 'deleteConfirmation'])->name('categories.delete-confirmation')->middleware('auth');


//profile
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    // transactions
   Route::get('/admin/transactions', [TransactionController::class, 'index'])->name('transactions.index');
});


