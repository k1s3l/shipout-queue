<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailRequest;
use App\Http\Requests\EmailsRequest;
use App\Mail\EmailConfirmed;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class ConfirmController extends Controller
{
    public function index(EmailRequest $request)
    {
        $mailable = Mail::to($request->validated())
            ->later(
                now()->addSecond(5),
                (new EmailConfirmed())->onConnection('redis')->onQueue('email')
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
            (new EmailConfirmed())
                ->onConnection('redis')
                ->onQueue('email')
        );

        return response()->json([
            'success' => true,
            'mailable_id' => $messages,
        ]);
    }

    public function item()
    {
        $queue = Redis::connection()->client()
            ->zRange('queues:email:delayed', 0, -1);

        response()->json($queue);
    }
}
