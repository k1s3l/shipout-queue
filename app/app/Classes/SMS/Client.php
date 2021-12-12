<?php

namespace App\Classes\SMS;

use App\Classes\AbstractClient;

class Client extends AbstractClient
{
    public const BASE_URI = '/sms/send';

    public function send(string $to, string $msg)
    {
        $uri = $this->client::BASE_URL . self::BASE_URI;
        $query = $this->client->getCredential() + ['to' => $to, 'msg' => $msg, 'json' => 1];

        $response = $this->client->getHttpClient()->get($uri, [
            'query' => $query,
        ]);
    }
}
