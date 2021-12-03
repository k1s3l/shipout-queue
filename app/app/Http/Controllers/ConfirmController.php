<?php

namespace App\Http\Controllers;

use App\Classes\EmailHandle;
use App\Classes\PushHandle;
use App\Classes\SmsHandle;
use App\Http\Requests\ConfirmTokenRequest;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\EmailsRequest;
use App\Http\Requests\SmsRequest;
use App\Jobs\SmsNexmo;
use App\Mail\EmailConfirmed;
use App\Models\ConfirmToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Nexmo\Laravel\Facade\Nexmo;
use Symfony\Component\HttpFoundation\Request;

class ConfirmController extends Controller
{
    public function index(EmailRequest $request)
    {
        $mailable = Mail::to($request->validated())
            ->later(
                now()->addSecond(5),
                (new EmailConfirmed(mt_rand(000000, 999999)))->onConnection('redis')->onQueue('email')
            );

        return response()->json([
            'success' => true,
            'mailable_id' => $mailable,
        ]);
    }

    public function emails(EmailsRequest $request)
    {
        $emails = collect($request->validated()['emails'])
            ->map(static function ($item) {
                return [
                    'email' => $item,
                ];
            });

        $messages = Mail::to($emails)->later(
            now()->addSecond(5),
            (new EmailConfirmed(mt_rand(000000, 999999)))
                ->onConnection('redis')
                ->onQueue('email')
        );

        return response()->json([
            'success' => true,
            'mailable_id' => $messages,
        ]);
    }

    public function sms(SmsRequest $request)
    {
//        https://laravel.demiart.ru/laravel-sozdayom-svoi-sobstvennye-funktsii/
//        https://laravel.demiart.ru/macros/
//        $chain = new PushHandle();
//        $chain->setHandle(new SmsHandle())->setHandle(new EmailHandle());
//        $channel = $chain->handle(User::where(['email' => 'iks3lewil@yandex.ru'])->first());

//        return response()->json(['channel' => $channel]);

        $validation = Validator::make(['phone' => $request->phone], [
            'phone' => Rule::phone()->country(['RU']),
        ]);

        [$code, $token]  = [Str::random(6), Str::random(64)];

        if (!$is_us = $validation->errors()->count()) {
            dispatch(new SmsNexmo($request->phone, $code))
                ->onConnection('redis')
                ->onQueue('sms');
        }

        ConfirmToken::create([
            'code' => $code,
            'token' => $token,
        ]);

        return response()->json([
            'success' => true,
            'is_us' => (bool)$is_us,
            'token' => $token,
        ]);
    }

    public function smsCode(ConfirmTokenRequest $request, ConfirmToken $token)
    {
        $this->validate($request, [
            'code' => [
                static fn ($attribute, $value, $fail) => $value == $token->code ?: $fail('Неверный код'),
            ],
        ]);

        return response()->json(['success' => true]);
    }
}
