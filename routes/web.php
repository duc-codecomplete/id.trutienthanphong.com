<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

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

Route::get('/dang-ky', [AuthController::class, 'signup']);
Route::get('/dang-nhap', [AuthController::class, 'signin'])->name("login");

Route::post('/dang-ky', [AuthController::class, 'signupPost']);
Route::post('/dang-nhap', [AuthController::class, 'signinPost']);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [HomeController::class, 'home'])->name("home");
    Route::get('/update_char', [AuthController::class, 'updateChar'])->name("update_char");
    Route::post('/set_main_char', [AuthController::class, 'setMainChar']);
    Route::get('/set_main_char/{id}', [AuthController::class, 'setMainCharHome']);
    Route::get('/doi-mon-phai/{id}', [AuthController::class, 'changeClassGet']);
    Route::post('/doi-mon-phai/{id}', [AuthController::class, 'changeClassPost']);
    Route::get('/logout', function () {
        Auth::logout();
        return redirect("/dang-nhap");
    });

    Route::get('/knb', [HomeController::class, 'getKnb'])->name("knb");
    Route::post('/knb', [HomeController::class, 'postKnb']);

    Route::get('/nap-tien', [HomeController::class, 'getNapTien'])->name("payment");
    Route::get('/lich-su-nap-tien', [HomeController::class, 'histories'])->name("histories");
    Route::get('/shops', [HomeController::class, 'getShop'])->name("shops");
    Route::post('/shops', [HomeController::class, 'postShop']);

    Route::get('/giftcodes', [HomeController::class, 'getGiftCode'])->name("giftcodes");
    Route::post('/giftcodes', [HomeController::class, 'useGiftCode']);
    Route::get('/transactions', [HomeController::class, 'transactions'])->name("transactions");

    Route::get('/doi-mat-khau', [AuthController::class, 'getPassword'])->name("password");
    Route::post('/doi-mat-khau', [AuthController::class, 'postPassword']);

    Route::get('/online', [HomeController::class, 'online'])->name("online");
    Route::get('/lich-su-mua', [HomeController::class, 'shopHistory'])->name("shopHistory");
    Route::get('/vip', [HomeController::class, 'vip'])->name("vip");
    Route::get('/chat', [HomeController::class, 'chat'])->name("chat");
});
