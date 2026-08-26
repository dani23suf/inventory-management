<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [
    ProductController::class,
    'index',
]);

Route::post('/stock/in', [
    StockController::class,
    'stockIn',
]);

Route::post('/stock/out', [
    StockController::class,
    'stockOut',
]);

Route::get('/warehouses', [
    WarehouseController::class,
    'index',
]);

Route::post('/stock/transfer', [
    StockController::class,
    'transfer',
]);

Route::get('/stock/report', [
    StockController::class,
    'report',
]);

Route::get('/products/{product}', [
    ProductController::class,
    'show',
]);