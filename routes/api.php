<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\AnalogPaymentController;

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
Route::post('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/auth/google/callback-api', [AuthController::class, 'handleGoogleCallbackNew']);

Route::post('/profile/change-password',[ProfileController::class,'change_password'])->middleware('auth:sanctum');
Route::post('/profile/update-profile',[ProfileController::class,'update_profile'])->middleware('auth:sanctum');

Route::get('/categories', [CategoryController::class, 'allCategories']);
Route::get('/categories/book', [CategoryController::class, 'getBooks']);
Route::get('/categories/course', [CategoryController::class, 'getCourses']);
Route::get('/categories/search-book', [CategoryController::class, 'searchByName']);
Route::get('/categories/search-course', [CategoryController::class, 'searchCourseByName']);
Route::get('/booksByCategory/{category_id}', [CategoryController::class, 'booksByCategoryId']);
Route::get('/books', [BookController::class, 'getBooks']);
Route::get('/book/{id}', [BookController::class, 'getBookDetails']);
Route::get('/search', [BookController::class,'search']);
Route::get('/pdf-url/{id}', [BookController::class, 'getPdfBook']);
Route::post('/pdf-url/{id}', [BookController::class, 'updatePdfBook']);
Route::get('/fetch-image', [BookController::class, 'fetchImage']);
Route::get('/courses', [CourseController::class, 'getCourses']);
Route::get('/course-chapters/{id}', [CourseController::class, 'getCourseChapters']);
Route::group(['prefix' => 'chat'], function () {
    Route::get('/messages/{user_id}', [ChatController::class, 'getMessages']);
    Route::post('send', [ChatController::class, 'sendMessage']);
    Route::delete('/messages/{id}', [ChatController::class, 'deleteMessage']);
});



Route::middleware('auth:sanctum')->group(function () {
    // Toggle like for a video
    Route::post('videos/{videoId}/like', [VideoController::class, 'toggleLike']);

    // Toggle dislike for a video
    Route::post('videos/{videoId}/dislike', [VideoController::class, 'toggleDislike']);

    // Add comment to a video
    Route::post('videos/{videoId}/comments', [VideoController::class, 'addComment']);

    // Delete comment by ID
    Route::delete('comments/{commentId}', [VideoController::class, 'deleteComment']);

    // Add reply to a comment
    Route::post('comments/{commentId}/replies', [VideoController::class, 'addReply']);

    // Delete reply by ID
    Route::delete('replies/{replyId}', [VideoController::class, 'deleteReply']);

    // Edit a comment
    Route::post('comments/{commentId}', [VideoController::class, 'editComment'])->name('comments.edit');

    // Edit a reply
    Route::post('replies/{replyId}', [VideoController::class, 'editReply'])->name('replies.edit');

    // Get all replies for a comment
    Route::get('comments/{commentId}/replies', [VideoController::class, 'getRepliesForComment'])->name('comments.replies');

    // Filter comments by order (oldest to newest or newest to oldest)
    Route::get('comments/filter/{videoId}', [VideoController::class, 'getComments'])->name('comments.filter');
    //Route::get('/videos/{videoId}/comments-replies',[VideoController::class, 'getCommentsWithReplies']);

});

// Get all videos for a book with like, dislike, and comment count
Route::get('books/{bookId}/videos', [VideoController::class, 'getVideosForBook']);
Route::get('/videos/{videoId}/comments-replies',[VideoController::class, 'getCommentsWithReplies']);
Route::post('/videos/details', [VideoController::class, 'getVideoDetails']);

//payment
Route::post('/analog-payment', [AnalogPaymentController::class, 'store']);
// Route to get all payment methods
Route::get('/payment-methods', [AnalogPaymentController::class, 'getAllPaymentMethods']);

// Route to get all analog payments for a particular user by user id
Route::get('/analog-payments/user/{userId}', [AnalogPaymentController::class, 'getAnalogPaymentsByUserId']);

