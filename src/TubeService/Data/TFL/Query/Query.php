<?php

namespace TubeService\Data\TFL\Query;

use GuzzleHttp\ClientInterface;

class Query
{

    const BASE_URL = '';

    protected $httpClient;
    protected $appID;
    protected $appKey;

    public function __construct(
        ClientInterface $httpClient,
        $appID,
        $appKey
    ) {
        $this->httpClient = $httpClient;
        $this->appID = $appID;
        $this->appKey = $appKey;
    }

    protected function getUrl(): string
    {
        $query = http_build_query([
            'app_id' => $this->appID,
            'app_key' => $this->appKey
        ]);
        return static::BASE_URL . '?' . $query;
    }

    protected function getData(string $url): array
    {
        $response = $this->httpClient->request('GET', $url);
        $body = $response->getBody();
        return json_decode($body);
    }
}