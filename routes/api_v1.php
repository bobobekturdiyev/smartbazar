<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('/merchants', \App\Http\Controllers\Api\V1\MerchantController::class);
Route::apiResource('/shops', \App\Http\Controllers\Api\V1\ShopController::class);

Route::delete('/merchants/{merchant_id}/delete-all-shop', [\App\Http\Controllers\Api\V1\ShopController::class, 'deleteAllShops']);
Route::get('/merchants/{merchant_id}/shops', [\App\Http\Controllers\Api\V1\ShopController::class, 'getAllShops']);
Route::post('/merchants/{merchant_id}/shops', [\App\Http\Controllers\Api\V1\ShopController::class, 'getNearestShop']);
