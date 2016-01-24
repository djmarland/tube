<?php

namespace IngestService\Service;

use GuzzleHttp\ClientInterface;

class PushService
{
    const CHROME_URL = 'https://android.googleapis.com/gcm/send';

    protected $httpClient;

    protected $chromeServerKey;

    public function __construct(
        ClientInterface $httpClient,
        string $chromeServerKey
    ) {
        $this->httpClient = $httpClient;
        $this->chromeServerKey = $chromeServerKey;
    }

    public function notifyChromeUsers($subscriptions) {
        $maxChunk = 1000;

        if (count($subscriptions) > $maxChunk) {
            // split anything larger than $maxChunk
            $splits = array_chunk($subscriptions, $maxChunk);
            $result = null;
            foreach ($splits as $split) {
                $result = $this->notifyChromeUsers($split);
            }
            return $result;
        }

        $userIDs = array_map(function ($sub) {
            return $sub->getChromeRegistrationId();
        }, $subscriptions);

        if (empty($userIDs)) {
            return null;
        }

        $data = (object) [
            'registration_ids' => $userIDs
        ];

        $response = $this->httpClient->request('POST', self::CHROME_URL, [
            'headers' => [
                'Authorization' => 'key=' . $this->chromeServerKey
            ],
            'json' => $data
        ]);

        return $response;
    }

    public function notifyFirefoxUsers($subscriptions)
    {
        // can't support this yet
    }
}
