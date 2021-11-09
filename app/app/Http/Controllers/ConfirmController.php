<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailRequest;
use App\Http\Requests\EmailsRequest;
use App\Http\Requests\SmsRequest;
use App\Jobs\SmsNexmo;
use App\Mail\EmailConfirmed;
use App\Models\ConfirmToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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
        $validation = Validator::make(['phone' => $request->phone], [
            'phone' => Rule::phone()->country(['RU']),
        ]);

        $code = Str::random(6);
        $token = Str::random(64);

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

    public function smsCode(Request $request, $token)
    {
        $confirmToken = ConfirmToken::where(['token' => $token])->first();
        if ($confirmToken->code == $request->get('code') && $confirmToken->confirmed) {
            $confirmToken->confirmed = false;
            $confirmToken->save();

            return response()->json($confirmToken->toArray() + ['success' => 'Указан верный код']);
        }

        return response()->json(['success' => 'Указан неверный код']);
    }
}
