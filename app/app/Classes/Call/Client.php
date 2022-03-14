<?php

namespace App\Classes\Call;

use App\Classes\AbstractClient;

class Client extends AbstractClient
{
    public const BASE_URI = '/callcheck/add';

    public function check(string $phone)
    {
        $uri = $this->client::BASE_URL . self::BASE_URI;
        $query = $this->client->getCredential() + ['phone' => $phone, 'json' => 1];

        return $this->client->getHttpClient()->get($uri, [
            'query' => $query,
        ]);
    }
}
