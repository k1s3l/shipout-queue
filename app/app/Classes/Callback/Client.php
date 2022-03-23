<?php

namespace App\Classes\Callback;

use App\Classes\AbstractClient;

class Client extends AbstractClient
{
    public const BASE_URI = '/callback';

    public const ADD_URI= '/add';

    public const GET_URI= '/get';

    public const DEL_URI= '/del';

    public function add(string $callback_url)
    {
        $uri = $this->getBaseUrl() . self::ADD_URI;
        $query = $this->client->getCredential() + ['url' => $callback_url, 'json' => 1];

        return $this->sendRequest($uri, $query);
    }

    public function get()
    {
        $uri = $this->getBaseUrl() . self::GET_URI;
        $query = $this->client->getCredential() + ['json' => 1];

        return $this->sendRequest($uri, $query);
    }

    public function remove(string $url)
    {
        $uri = $this->getBaseUrl() . self::DEL_URI;
        $query = $this->client->getCredential() + ['url' => $url, 'json' => 1];

        return $this->sendRequest($uri, $query);
    }

    public function getBaseUrl(): string
    {
        return $this->client::BASE_URL . self::BASE_URI;
    }

    public function sendRequest(string $uri, array $query)
    {
        return $this->client->getHttpClient()->get($uri, [
            'query' => $query,
        ]);
    }

}
