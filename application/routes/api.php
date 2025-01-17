<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::match(['get', 'post'], 'amocrm/redirect', [AuthController::class => 'redirect'])->name('amocrm_redirect');

Route::post('hook/talks', [HookController::class => 'talks']);


//3) Количество состоявшихся замеров. Фильтрация по менеджерам и по назначенным замерщикам. также по источникам лидов
