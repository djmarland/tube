<?php

namespace IngestService\Service;

use GuzzleHttp\ClientInterface;

class PushChromeService
{
    const BASE_URL = 'https://android.googleapis.com/gcm/send';

    protected $httpClient;

    protected $serverKey;

    public function __construct(
        ClientInterface $httpClient,
        string $serverKey
    ) {
        $this->httpClient = $httpClient;
        $this->serverKey = $serverKey;
    }

    public function notifyUsers($userIDs) {
        if (count($userIDs) > 1000) {
            // @todo - split the request
            throw new \Exception('cannot support this many yet');
        }

        if (empty($userIDs)) {
            return null;
        }

        $data = (object) [
            'registration_ids' => $userIDs
        ];

        $response = $this->httpClient->request('POST', self::BASE_URL, [
            'headers' => [
                'Authorization' => 'key=' . $this->serverKey
            ],
            'json' => $data
        ]);

        return $response;
    }
}
