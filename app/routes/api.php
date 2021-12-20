<?php

use App\Http\Controllers\ConfirmController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\SmsWebhook;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => '/confirm'], static function () {

    Route::group(['prefix' => 'email'], static function () {
        Route::get('/', [ConfirmController::class, 'index']);
        Route::post('/code', [ConfirmController::class, 'index']);
        Route::get('/code', [ConfirmController::class, 'item']);
    });

    Route::group(['prefix' => 'sms'], static function () {
        Route::get('/', [ConfirmController::class, 'sms']);
        Route::post('/{token}', [ConfirmController::class, 'smsCode']);
    });

    Route::group(['prefix' => 'emails'], static function () {
        Route::post('/', [ConfirmController::class, 'emails']);
    });

    Route::group(['prefix' => 'webhook'], static function () {
        Route::post('/email/{token}', [WebhookController::class, 'email']);
        Route::post('/push/{token}', [WebhookController::class, 'push']);

        Route::post('/sms/{token}', [WebhookController::class, 'vonage'])
            ->middleware(SmsWebhook::class);

        Route::post('sms-ru', [WebhookController::class, 'smsRu'])
//            ->middleware(SmsWebhook::class)
            ->name('sms_ru_callback');
    });
});
