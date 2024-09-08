<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('products/dataTable', [ProductController::class, 'serverSideTable'])->name('products.serverSideTable');
Route::resource('products', ProductController::class)->except(['create', 'edit']);
