<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth:api',
], function ($router) {

    Route::apiResource('/merchants', \App\Http\Controllers\Api\V1\MerchantController::class);
    Route::apiResource('/shops', \App\Http\Controllers\Api\V1\ShopController::class);

    Route::delete('/merchants/{merchant_id}/delete-all-shop', [\App\Http\Controllers\Api\V1\ShopController::class, 'deleteAllShops']);
    Route::get('/merchants/{merchant_id}/shops', [\App\Http\Controllers\Api\V1\ShopController::class, 'getAllShops']);
    Route::post('/merchants/{merchant_id}/shops', [\App\Http\Controllers\Api\V1\ShopController::class, 'getNearestShop']);

});




Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});
