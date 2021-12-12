<?php

namespace App\Classes;

use App\Classes\Channels\SmsRuApi;

abstract class AbstractClient
{
    protected $client;

    public function __construct(SmsRuApi $client)
    {
        $this->client = $client;
    }
}
