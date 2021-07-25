<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountTransactionController;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\AccountVerificationController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TransactionEntryController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->name('api.')->group(function () {

    Route::group(['middleware' => 'guest:sanctum'], function () {
        Route::post('register', RegisterController::class)->name('register');

        Route::post('login', [AuthenticationController::class, 'store'])->name('login');
    });

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('logout', [AuthenticationController::class, 'destroy'])->name('logout');

        Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');

        Route::get('banks', [BankController::class, 'index'])->name('banks.index');

        Route::post('account/verify', AccountVerificationController::class)->name('verify-account');

        Route::post('transactions', [TransactionEntryController::class, 'store'])->name('transactions.store');

        Route::get('accounts/{account}/transactions', AccountTransactionController::class)->name('accounts.transactions.index');
    });

    Route::get('account-types', [AccountTypeController::class, 'index'])->name('account_types.index');
});
