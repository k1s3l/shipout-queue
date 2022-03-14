<?php

namespace App\Classes\Callback;

use App\Classes\AbstractClient;

class Client extends AbstractClient
{
    public const BASE_URI = '/callback/add';

    public function add(string $callback_url)
    {
        $uri = $this->client::BASE_URL . self::BASE_URI;
        $query = $this->client->getCredential() + ['url' => $callback_url, 'json' => 1];

        return $this->client->getHttpClient()->get($uri, [
            'query' => $query,
        ]);
    }
}
