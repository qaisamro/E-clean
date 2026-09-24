<?php

use App\Http\Controllers\API\Banner\BannerController as ApiBannerController;
use App\Http\Controllers\API\Promotion\PromotionController;
use App\Http\Controllers\API\Service\ServiceController as ApiServiceController;
use App\Http\Controllers\API\Vendor\VendorController as ApiVendorController;
use Illuminate\Support\Facades\Route;

Route::get('/banners', [ApiBannerController::class, 'index']);
Route::get('/promotions', [PromotionController::class, 'index']);
Route::get('/offers', [PromotionController::class, 'index']);
Route::get('/services', [ApiServiceController::class, 'index']);
Route::get('/vendors', [ApiVendorController::class, 'index']);
Route::get('/vendors/{vendor}/services', [ApiVendorController::class, 'services']);
Route::get('/vendors/{vendor}/products', [ApiVendorController::class, 'products']);