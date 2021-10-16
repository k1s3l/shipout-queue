<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailsRequest;
use App\Mail\EmailConfirmed;
use Illuminate\Support\Facades\Mail;

class ConfirmController extends Controller
{
    public function index(EmailsRequest $request)
    {
        $message = new EmailConfirmed(mt_rand(100000, 999999));

        Mail::to($request->validated())
            ->later(now()->addSecond(5), $message->onConnection('redis'));
    }
}
