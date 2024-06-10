<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    
    if(Auth::check()){
        return redirect()->route('products.index');
    }
    else{
        return redirect()->route('login');
    }
});

Auth::routes();

Route::get('products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::post('products',[App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
Route::get('products/create',[App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
Route::get('products/{product}',[App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
Route::put('products/{product}',[App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
Route::delete('products/{product}',[App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');
Route::get('products/{product}/edit',[App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
