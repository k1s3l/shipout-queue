<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmTokenRequest;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\EmailsRequest;
use App\Http\Requests\SmsRequest;
use App\Jobs\SMSNexmo;
use App\Jobs\SMSRu;
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
                (new EmailConfirmed(
                    Str::random(config('default.code_confirm_length'))
                ))
                    ->onConnection('redis')
                    ->onQueue('email')
            );

        return response()->json([
            'success' => true,
            'mailable_id' => $mailable,
        ]);
    }

    public function emails(EmailsRequest $request)
    {
        $messages = Mail::to($request->emails)->later(
            now()->addSecond(5),
            (new EmailConfirmed(
                Str::random(config('default.code_confirm_length'))
            ))
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

        [$code, $token]  = [
            Str::random(config('default.code_confirm_length')),
            Str::random(config('default.token_length'))
        ];

        // USA mask check
        if ($validation->errors()->count()) {
            dispatch(new SMSNexmo($request->phone, $code))
                ->onConnection('redis')
                ->onQueue('sms');
        } else {
            dispatch(new SMSRu($request->phone, $code))
                ->onConnection('redis')
                ->onQueue('sms');
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
