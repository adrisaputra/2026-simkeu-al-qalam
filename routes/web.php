<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionCodeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/buat_storage', function () {
    Artisan::call('storage:link');
    dd("Storage Berhasil Di Buat");
});

Route::get('/clear-cache-all', function() {
    Artisan::call('cache:clear');
    Artisan::call('route:cache');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    dd("Cache Clear All");
});



Route::get('/', [LoginController::class, 'index']);
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);


Route::middleware(['role:Admin Finance'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index']);
    
    ## Transaction Code
    Route::get('/transaction_code', [TransactionCodeController::class, 'index'])->name('transaction_code.index');
    Route::get('/transaction_code/list', [TransactionCodeController::class, 'get_transaction_code_index'])->name('transaction_code.list');
    Route::post('/transaction_code/store', [TransactionCodeController::class, 'store']);
    Route::post('/transaction_code/validate/{action}', [TransactionCodeController::class, 'validate']);
    Route::get('/transaction_code/edit/{transaction_code}', [TransactionCodeController::class, 'edit']);
    Route::put('/transaction_code/edit/{transaction_code}', [TransactionCodeController::class, 'update']);
    Route::get('/transaction_code/delete/{transaction_code}',[TransactionCodeController::class, 'delete']);

    ## User
    Route::get('/user', [UserController::class, 'index'])->name('users.index');
    Route::get('/user/list', [UserController::class, 'get_user_index'])->name('users.list');
    Route::post('/user/store', [UserController::class, 'store']);
    Route::post('/user/validate/{action}', [UserController::class, 'validate']);
    Route::get('/user/edit/{user}', [UserController::class, 'edit']);
    Route::put('/user/edit/{user}', [UserController::class, 'update']);
    Route::get('/user/delete/{user}',[UserController::class, 'delete']);

    ## Log
    Route::get('/log', [LogController::class, 'index'])->name('logs.index');
    Route::get('/log/list', [LogController::class, 'get_log_index'])->name('logs.list');
    Route::get('/log/detail/{user}', [LogController::class, 'detail']);

    ## Setting
    Route::get('/setting', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/setting/validate', [SettingController::class, 'validate']);
    Route::put('/setting/edit/{setting}', [SettingController::class, 'update']);
   
});