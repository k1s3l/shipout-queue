<?php

namespace App\Http\Controllers;

use App\Models\ConfirmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebhookController extends Controller
{
    public function vonage()
    {

    }

    public function smsRu(Request $request)
    {
        return response()->json(100);
    }

    public function email()
    {

    }

    public function push()
    {

    }
}
