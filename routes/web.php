<?php

use App\Http\Controllers\ClassesController;
use App\Http\Controllers\DescCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TransactionCodeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkUnitController;
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
    
    ## Income
    Route::get('/income', [IncomeController::class, 'index'])->name('income.index');
    Route::get('/income/list', [IncomeController::class, 'get_income_index'])->name('income.list');
    Route::get('/income/{income}/transaction-banks', [IncomeController::class, 'get_bank_transactions'])->name('income.transaction_banks');
    Route::post('/income/store', [IncomeController::class, 'store']);
    Route::post('/income/validate/{action}', [IncomeController::class, 'validate']);
    Route::get('/income/edit/{income}', [IncomeController::class, 'edit']);
    Route::put('/income/edit/{income}', [IncomeController::class, 'update']);
    Route::get('/income/delete/{income}',[IncomeController::class, 'delete']);
    Route::post('/income/import', [IncomeController::class, 'import']);

    ## Expense
    Route::get('/expense', [ExpenseController::class, 'index'])->name('expense.index');
    Route::get('/expense/list', [ExpenseController::class, 'get_expense_index'])->name('expense.list');
    Route::post('/expense/store', [ExpenseController::class, 'store']);
    Route::post('/expense/validate/{action}', [ExpenseController::class, 'validate']);
    Route::get('/expense/edit/{expense}', [ExpenseController::class, 'edit']);
    Route::put('/expense/edit/{expense}', [ExpenseController::class, 'update']);
    Route::get('/expense/delete/{expense}',[ExpenseController::class, 'delete']);
    
    ## Print
    Route::get('/print', [PrintController::class, 'index'])->name('print.index');
    Route::post('/print', [PrintController::class, 'print']);

    ## Work Unit
    Route::get('/work_unit', [WorkUnitController::class, 'index'])->name('work_unit.index');
    Route::get('/work_unit/list', [WorkUnitController::class, 'get_work_unit_index'])->name('work_unit.list');
    
    ## Classes
    Route::get('/classes/{work_unit}', [ClassesController::class, 'index'])->name('classes.index');
    Route::get('/classes/list/{work_unit}', [ClassesController::class, 'get_classes_index'])->name('classes.list');
    Route::post('/classes/store', [ClassesController::class, 'store']);
    Route::post('/classes/validate/{action}', [ClassesController::class, 'validate']);
    Route::get('/classes/edit/{classes}', [ClassesController::class, 'edit']);
    Route::put('/classes/edit/{classes}', [ClassesController::class, 'update']);
    Route::get('/classes/delete/{classes}',[ClassesController::class, 'delete']);

    ## Student
    Route::get('/student/{classes}', [StudentController::class, 'index'])->name('student.index');
    Route::get('/student/list/{classes}', [StudentController::class, 'get_student_index'])->name('student.list');
    Route::post('/student/store', [StudentController::class, 'store']);
    Route::post('/student/validate/{action}', [StudentController::class, 'validate']);
    Route::get('/student/edit/{student}', [StudentController::class, 'edit']);
    Route::put('/student/edit/{student}', [StudentController::class, 'update']);
    Route::get('/student/delete/{student}',[StudentController::class, 'delete']);
    Route::post('/student/import', [StudentController::class, 'import']);

    ## Desc Category
    Route::get('/desc_category', [DescCategoryController::class, 'index'])->name('desc_category.index');
    Route::get('/desc_category/list', [DescCategoryController::class, 'get_desc_category_index'])->name('desc_category.list');
    Route::post('/desc_category/store', [DescCategoryController::class, 'store']);
    Route::post('/desc_category/validate/{action}', [DescCategoryController::class, 'validate']);
    Route::get('/desc_category/edit/{desc_category}', [DescCategoryController::class, 'edit']);
    Route::put('/desc_category/edit/{desc_category}', [DescCategoryController::class, 'update']);
    Route::get('/desc_category/delete/{desc_category}',[DescCategoryController::class, 'delete']);

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