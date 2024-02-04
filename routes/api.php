<?php

use App\Http\Controllers\AuthController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgot_password']);
Route::post('/verify-otp', [AuthController::class, 'verify_otp']);
Route::post('/reset-password', [AuthController::class, 'reset_password']);


Route::get('/dropdowns', 'App\Http\Controllers\DropdownController@index');
Route::post('/mail/website-mail', 'App\Http\Controllers\MailController@website_mail');

Route::resource('users', 'App\Http\Controllers\UserController');
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::resource('luggage-types', 'App\Http\Controllers\LuggageTypeController');
    Route::resource('trips', 'App\Http\Controllers\TripController');
    Route::resource('orders', 'App\Http\Controllers\OrderController');
    Route::resource('stripe', 'App\Http\Controllers\StripeController');
});
