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


Route::get('/dropdowns', 'App\Http\Controllers\DropdownController@index');
Route::post('/mail/website-mail', 'App\Http\Controllers\MailController@website_mail');
Route::resource('orders', 'App\Http\Controllers\OrderController');
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::resource('users', 'App\Http\Controllers\UserController');
    Route::resource('luggage-types', 'App\Http\Controllers\LuggageTypeController');
    Route::resource('trips', 'App\Http\Controllers\TripController');
});
