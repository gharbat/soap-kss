<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post("/activate", [\App\Http\Controllers\SubscriptionController::class, "activate"]);
Route::get("/soft_cancel", [\App\Http\Controllers\SubscriptionController::class, "softCancel"]);
Route::post("/renew", [\App\Http\Controllers\SubscriptionController::class, "Renew"]);
Route::post("/resume", [\App\Http\Controllers\SubscriptionController::class, "Resume"]);
Route::post("/pause", [\App\Http\Controllers\SubscriptionController::class, "Pause"]);
Route::post("/hard_cancel", [\App\Http\Controllers\SubscriptionController::class, "HardCancel"]);
Route::post("/get_info", [\App\Http\Controllers\SubscriptionController::class, "GetInfo"]);




