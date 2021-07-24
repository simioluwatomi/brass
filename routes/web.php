<?php

use App\Http\Controllers\PaystackTransferWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('webhooks/paystack/transfer', PaystackTransferWebhookController::class)
    ->name('webhooks.paystack.transfer');

