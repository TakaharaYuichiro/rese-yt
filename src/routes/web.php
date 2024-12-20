<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendancesController;
use App\Http\Controllers\RegisterdUserController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;


use App\Http\Controllers\ShopAllController;
use App\Http\Controllers\ShopDetailController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MypageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::controller(EmailVerificationController::class)
	->prefix('email')->name('verification.')->group(function () {
		// 確認メール送信画面
		// Route::get('verify', 'index')->name('notice');
		// Route::get('verify', 'index')->middleware('auth')->name('notice');

		// 確認メール送信
		Route::post('verification-notification', 'notification')
			->middleware('throttle:6,1')->name('send');

		// 確認メールリンクの検証
		Route::get('verification/{id}/{hash}', 'verification')
			->middleware(['signed', 'throttle:6,1'])->name('verify');
		
		// 確認メール再送
		Route::get('resend_verify_email', 'resendVerifyEmail');

		// セッションリセット（確認メールが届かないなどイレギュラー時の対応）
		Route::get('reset', 'resetSession');
	});



// ログイン関連のページ
Route::post('/register',[RegisterdUserController::class,'store']);
Route::post('/login',[AuthenticatedSessionController::class,'store']);
Route::post('/logout',[AuthenticatedSessionController::class,'destroy']);

// 認証が必要なページ
Route::middleware(['web', 'verified', 'auth'])->group(function () {
    // Route::get('/', [AttendancesController::class, 'index']);
    Route::post('/store', [AttendancesController::class, 'store']);
    Route::get('/attendance', [AttendancesController::class, 'attendance']);
    Route::get('/personal', [AttendancesController::class, 'personal']);

    Route::post('/reset_all', [AttendancesController::class, 'resetAll']);
    Route::post('/reset_end', [AttendancesController::class, 'resetEnd']);
});

Route::get('/', [ShopAllController::class, 'index'])->name('home');
Route::post('/search', [ShopAllController::class, 'search']);
Route::post('/favorite', [ShopAllController::class, 'favorite']);

// Route::get('/detail', [ShopDetailController::class, 'index']);
Route::get('/detail/{shop_id}', [ShopDetailController::class, 'index'])->name('detail');

Route::post('/reservation/confirm', [ReservationController::class, 'confirm']);
Route::post('/reservation/store', [ReservationController::class, 'store']);	// exists_reservation_id==0ならcreate, >0ならupdateと使い分ける
// Route::post('/reservation/delete', [ReservationController::class, 'delete']);
// Route::post('/reservation/update', [ReservationController::class, 'update']);
Route::post('/reservation/delete', [ReservationController::class, 'delete']);
Route::post('/reservation/reservation_change', [ReservationController::class, 'reservationChange']);	// 予約内容画面を開く(実際のupdateはstoreで実行)

Route::get('/mypage', [MypageController::class, 'index']);
Route::post('/mypage/favorite', [MypageController::class, 'favorite']);

