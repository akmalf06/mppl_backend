<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\SpendController;
use App\Http\Controllers\StockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->name('auth.')->group(function(){
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});
Route::get('/me', [AuthController::class, 'me'])->middleware('auth')->name('me');

Route::prefix('branch')->name('branch.')->middleware('auth')->group(function(){
    Route::get('/', [BranchController::class, 'index'])->name('index');
    Route::post('/', [BranchController::class, 'store'])->name('index');
    Route::prefix('/{branchId}')->group(function(){
        Route::get('/', [BranchController::class, 'show'])->name('show');
        Route::prefix('income')->name('income.')->group(function(){
            Route::get('/', [IncomeController::class, 'index'])->name('index');
            Route::post('/', [IncomeController::class, 'store'])->name('store');
        });
        
        Route::prefix('spend')->name('spend.')->group(function(){
            Route::get('/', [SpendController::class, 'index'])->name('index');
            Route::post('/', [SpendController::class, 'store'])->name('store');
        });
        
        Route::prefix('stock')->name('stock.')->group(function(){
            Route::get('/{id}', [StockController::class, 'show'])->name('show');
            Route::put('/{id}', [StockController::class, 'update'])->name('update');
            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::post('/', [StockController::class, 'store'])->name('store');
        });
    });

});

