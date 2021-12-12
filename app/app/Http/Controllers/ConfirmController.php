<?php

namespace App\Http\Controllers;

use App\Classes\Channels\SmsRuApi;
use App\Classes\Decorators\DefaultDecorator;
use App\Classes\Decorators\TimeDecorator;
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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
        $validation = Validator::make(['phone' => $request->phone], [
            'phone' => Rule::phone()->country(['RU']),
        ]);

        [$code, $token]  = [Str::random(6), Str::random(64)];

        // is us or not
        if ($validation->errors()->count()) {
            dispatch(new SmsNexmo($request->phone, $code))
                ->onConnection('redis')
                ->onQueue('sms');
        } else {
            app(SmsRuApi::class)->sms()->send($request->phone, $code);
        }

        ConfirmToken::create([
            'code' => $code,
            'token' => $token,
        ]);

        return response()->json([
            'success' => true,
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

        if (now()->greaterThanOrEqualTo($token->expired_at)) {
            throw ValidationException::withMessages([
                'expired_at' => 'Время жизни кода подтверждения истекло'
            ]);
        }

        return response()->json(['success' => true]);
    }
}
