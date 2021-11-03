<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailRequest;
use App\Http\Requests\EmailsRequest;
use App\Mail\EmailConfirmed;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
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

    public function sms(Request $request)
    {
        app('Nexmo\Client')->message()->send([
            'from' => 'VONAGE',
            'to' => $request->get('phone'),
            'text' => 'Code is: ' . mt_rand(0000, 9999),
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}
