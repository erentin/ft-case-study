<?php

use App\Http\Controllers\Api\V1\NewsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('/search-by-symbol', [NewsController::class,  'searchBySymbol']);
    Route::post('/search-by-time', [NewsController::class, 'searchByTime']);

});
