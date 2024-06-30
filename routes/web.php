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
use App\Http\Controllers\PdfController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\PaymentMethodController;

Route::get('/admin/config', [AppConfigController::class, 'index'])->name('admin.config')->middleware('auth');
Route::post('/admin/update-notification-preference', [AppConfigController::class, 'updateNotificationPreference'])->name('admin.updateNotificationPreference')->middleware('auth');
Route::get('/admin/dashboard', [AppConfigController::class, 'dashboard'])->name('admin.dashboard')->middleware('auth');

//chat
Route::get('/user-messages', [ChatMessageController::class, 'index'])->name('user-messages')->middleware('auth');
Route::get('/user-chats/{user}', [ChatMessageController::class, 'showUserChats'])->name('user-chats')->middleware('auth');
Route::post('/user-chats/{user}/reply', [ChatMessageController::class, 'reply'])->name('user-chats.reply')->middleware('auth');
Route::delete('/delete-chat/{id}', [ChatMessageController::class, 'delete'])->name('delete-chat')->middleware('auth');

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
Route::get('/book-user/{user}/edit', [AuthController::class, 'editUser'])->name('users.edit-user')->middleware('auth');
Route::put('/book-user/{user}', [AuthController::class, 'updateUser'])->name('users.update-user')->middleware('auth');
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
Route::post('/upload/course/video', [ChapterController::class, 'upload'])->name('upload.video');
Route::post('/upload/course/pdf', [ChapterController::class, 'uploadPdf'])->name('upload.pdf');
Route::post('/upload/pdfBook', [ChapterController::class, 'uploadPdfBook'])->name('upload.pdfBook');
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
   Route::put('/analog-payment/{id}/update-status', [TransactionController::class, 'updateStatus'])->name('admin.payments.updateStatus');
   Route::delete('/payments/{id}', [TransactionController::class, 'destroy'])->name('payments.destroy');


    Route::get('/pdf/getText/{id}', [PdfController::class, 'showPDF'])->name('books.show-pdf');


    //video on book
    // Route to show the video creation form
    Route::get('/books/{bookId}/videos/create', [VideoController::class, 'create'])->name('videos.create');

    // Route to store the newly created video
    Route::post('/books/{bookId}/videos', [VideoController::class, 'store'])->name('videos.store');

    // Route to show the video edit form
    Route::get('/books/{bookId}/videos/{videoId}/edit', [VideoController::class, 'edit'])->name('videos.edit');

    // Route to update the video
    Route::put('/books/{bookId}/videos/{videoId}', [VideoController::class, 'update'])->name('videos.update');

    // Route to delete the video
    Route::delete('/books/{bookId}/videos/{videoId}', [VideoController::class, 'destroy'])->name('videos.destroy');
    Route::post('/upload/video', [VideoController::class, 'upload'])->name('book.video');
    Route::post('/upload/pdf', [VideoController::class, 'uploadPdf'])->name('book.video.pdf');
    Route::get('/admin/comments', [VideoController::class, 'comments'])->name('admin.comments');
    Route::delete('/comments/{comment}', [VideoController::class, 'destroyComment'])->name('admin.comments.destroy');
    Route::delete('/replies/{reply}', [VideoController::class, 'destroyReply'])->name('admin.replies.destroy');


    // Index route - to list all payment details
    Route::get('/admin/payment_details', [PaymentMethodController::class, 'index'])->name('payment_details.index');

    // Create route - to show the form for creating a new payment detail
    Route::get('/admin/payment_details/create', [PaymentMethodController::class, 'create'])->name('payment_details.create');

    // Store route - to store a newly created payment detail
    Route::post('/admin/payment_details', [PaymentMethodController::class, 'store'])->name('payment_details.store');

    // Show route - to display a specific payment detail (optional, if needed)
    // Route::get('/admin/payment_details/{payment_detail}', [PaymentMethodController::class, 'show'])->name('payment_details.show');

    // Edit route - to show the form for editing an existing payment detail
    Route::get('/admin/payment_details/{payment_detail}/edit', [PaymentMethodController::class, 'edit'])->name('payment_details.edit');

    // Update route - to update a specific payment detail
    Route::put('/admin/payment_details/{payment_detail}', [PaymentMethodController::class, 'update'])->name('payment_details.update');

    // Destroy route - to delete a specific payment detail
    Route::delete('/admin/payment_details/{payment_detail}', [PaymentMethodController::class, 'destroy'])->name('payment_details.destroy');


});

