<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SubscribeController;

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

//Admin permission
Route::group(['middleware' => ['permission:admin', 'auth:sanctum']], function () {
    Route::post('post/create', [PostController::class, 'store'])->middleware('auth:sanctum');
    Route::post('post/{post}/update', [PostController::class, 'update']);
    Route::delete('post/{post}/delete', [PostController::class, 'delete']);
    Route::patch('post/{post}/restore', [PostController::class, 'restore']);
});

Route::get('user/{user:slug}/posts', [PostController::class, 'index']);
Route::get('user/{user:slug}/publishedPosts', [PostController::class, 'publishedPost']);
Route::get('/user_post', [PostController::class, 'usersPost'])->middleware('auth:sanctum');
Route::get('user/{user:slug}/post/{post}', [PostController::class, 'show']);
// Route::get('/post_by_category/{category}',[PostController::class, 'category'])->middleware('auth:sanctum');
Route::post('/post/update/status_draft', [PostController::class,'statusUpdateDraft']);
Route::post('/post/{post}/update/status_archive', [PostController::class,'statusUpdateArchive']);
Route::post('/post/tags', [PostController::class,'post_by_tags']);
Route::get('/post/views', [PostController::class,'post_views'])->middleware('auth:sanctum');
Route::get('/user/{user:slug}/tagList', [PostController::class,'all_tags']);
Route::get('user/{user:slug}/postStats', [PostController::class, 'postStats']);


Route::get('/comments/{post}', [CommentsController::class, 'index']);
Route::post('post/{post}/comments', [CommentsController::class, 'store'])->middleware('auth:sanctum');
Route::delete('comments/{comments}/delete', [CommentsController::class, 'delete'])->middleware('auth:sanctum');
Route::get('user/{user:slug}/comments', [CommentsController::class, 'dasboardComments']);


Route::post('post/{post}/likes', [LikesController::class, 'store'])->middleware('auth:sanctum');
Route::get('/post/{post}/counts', [LikesController::class, 'count']);
Route::get('/liked/{post}', [LikesController::class, 'userlike'])->middleware('auth:sanctum');
Route::get('/post_like/{post}', [LikesController::class,'lastfive']);


Route::post('user/{user:slug}/search', [SearchController::class, 'search']);


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('reset-password', [AuthController::class, 'reset']);
Route::post('forgot-password', [AuthController::class, 'forgetPassword']);
Route::post('checkEmail', [AuthController::class, 'checkEmail']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('profile', [AuthController::class, 'profile']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('check/bookmark', [BookmarkController::class, 'check']);
    Route::get("/profile", [AuthController::class, "profile"]);
    Route::post("/userProfile", [AuthController::class, "userProfile"]);
});

// Route::post('post/{post}/bookmark', [BookmarkController::class, 'add']);
// Route::delete('post/{post}/bookmark', [BookmarkController::class, 'remove']);
// Route::get('bookmark', [BookmarkController::class, 'get']);



Route::post("/upload", [ProfileController::class, "store"])->middleware('auth:sanctum');
Route::get('user/{user:slug}/user_details', [ProfileController::class,'all_users'])->middleware('auth:sanctum');
Route::get("/user/{user:slug}/profile", [ProfileController::class, "getUserProfile"]);


// Route::post('/category/create',[CategoryController::class,'store']);
// Route::post('/category/{category}/update',[CategoryController::class,'update']);
// Route::get('/category',[CategoryController::class,'index']);
// Route::get('/category/{category}',[CategoryController::class,'show']);



Route::post('user/{user:slug}/subscribe', [SubscribeController::class,'subscribe'])->middleware('auth:sanctum');
Route::get('user/{user:slug}/is_subscribe', [SubscribeController::class,'is_subscribe'])->middleware('auth:sanctum');


Route::get('/get_domain', [SiteController::class,'index']);
Route::post('/set_domain', [SiteController::class,'store']);
