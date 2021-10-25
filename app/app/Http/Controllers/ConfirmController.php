<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailsRequest;
use App\Mail\EmailConfirmed;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class ConfirmController extends Controller
{
    public function index(EmailsRequest $request)
    {
        $message = new EmailConfirmed(mt_rand(100000, 999999));

        $mailable = Mail::to($request->validated())
            ->later(
                now()->addSecond(5),
                $message->onConnection('redis')->onQueue('email')
            );

        return response()->json([
            'success' => true,
            'mailable_id' => $mailable,
        ]);
    }

    public function item()
    {
        $queue = Redis::connection()->client()
            ->zRange('queues:email:delayed', 0, -1);
    }
}
