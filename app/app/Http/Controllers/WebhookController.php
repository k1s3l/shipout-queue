<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function sms(Request $request)
    {
        logger($request->all());
    }

    public function email()
    {

    }

    public function push()
    {

    }
}
