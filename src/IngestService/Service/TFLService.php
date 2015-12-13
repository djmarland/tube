<?php

namespace IngestService\Service;

use GuzzleHttp\ClientInterface;
use IngestService\Domain\TFLLine;
use IngestService\Domain\TFLStatus;

class TFLService
{
    const TFL_BASE_URL = 'https://api.tfl.gov.uk/Line/Mode/tube,dlr,tflrail,overground/Status';

    protected $httpClient;

    protected $appID;

    protected $appKey;

    public function __construct(
        ClientInterface $httpClient,
        string $appID,
        string $appKey
    ) {
        $this->httpClient = $httpClient;
        $this->appID = $appID;
        $this->appKey = $appKey;
    }

    public function fetchLineStatuses() {
        $data = $this->fetchFromTFL();
        if (!$data) {
            return null;
        }
        $lines = [];
        // map into entities
        foreach ($data as $item) {
            $line = $this->mapItem($item);
            if ($line) {
                $lines[] = $line;
            }
        }
        return $lines;
    }

    private function fetchFromTFL()
    {
        $data = null;
        $query = http_build_query([
            'app_id' => $this->appID,
            'app_key' => $this->appKey
        ]);
        $url = self::TFL_BASE_URL . '?' . $query;

        $response = $this->httpClient->request('GET', $url);
        $body = $response->getBody();
        if (!empty($body)) {
            $data = json_decode($body);
        }
        return $data;
    }

    private function mapItem($item)
    {
        $statuses = [];
        foreach ($item->lineStatuses as $status) {
            $statuses[] = new TFLStatus(
                $status->statusSeverity,
                $status->statusSeverityDescription,
                $status->reason ?? null
            );
        }

        return new TFLLine(
            $item->id,
            $statuses
        );
    }
}
