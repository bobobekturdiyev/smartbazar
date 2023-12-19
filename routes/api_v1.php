<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('/merchants', \App\Http\Controllers\Api\V1\MerchantController::class);
