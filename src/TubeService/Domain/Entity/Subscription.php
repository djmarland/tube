<?php

namespace TubeService\Domain\Entity;

use TubeService\Domain\ValueObject\ID;
use DateTime;

class Subscription extends Entity
{
    const TYPE_CHROME = 'chrome';
    const TYPE_FIREFOX = 'firefox';

    public function __construct(
        ID $id,
        DateTime $createdAt,
        DateTime $updatedAt,
        string $endpoint,
        int $day,
        int $startHour,
        int $endHour,
        bool $isActive,
        string $publicKey = null,
        Line $line = null
    ) {
        parent::__construct(
            $id,
            $createdAt,
            $updatedAt
        );

        $this->endpoint = $endpoint;
        $this->publicKey = $publicKey;
        $this->day = $day;
        $this->startHour = $startHour;
        $this->endHour = $endHour;
        $this->isActive = $isActive;
        $this->line = $line;
    }

    private $line;

    public function getLine()
    {
        return $this->line;
    }
    public function setLine($line)
    {
        $this->line = $line;
    }

    private $isActive = true;

    public function isActive()
    {
        return $this->isActive;
    }

    private $day;

    public function getDay()
    {
        return $this->day;
    }

    private $startHour;

    public function getStartHour()
    {
        return $this->startHour;
    }

    private $endHour;

    public function getEndHour()
    {
        return $this->endHour;
    }

    private $endpoint;

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    private $publicKey;

    public function getPublicKey()
    {
        return $this->publicKey;
    }

    public function getType()
    {
        $endpoint = $this->getEndpoint();
        if (strpos($endpoint, 'https://android.googleapis.com/gcm/send') !== false) {
            return self::TYPE_CHROME;
        }
        if (strpos($endpoint, 'https://updates.push.services.mozilla.com') !== false) {
            return self::TYPE_FIREFOX;
        }

        return null;
    }

    public function isChrome()
    {
        return $this->getType() == self::TYPE_CHROME;
    }

    public function isFirefox()
    {
        return $this->getType() == self::TYPE_FIREFOX;
    }

    public function getChromeRegistrationId()
    {
        if (!$this->isChrome()) {
            return null;
        }
        $parts = explode('/', $this->getEndpoint());
        return end($parts);
    }

    public function isSupported()
    {
        return !!$this->getType();
    }

    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['endpoint'] = $this->getEndpoint();
        $data['isActive'] = $this->isActive();
        $data['day'] = $this->getDay();
        $data['startHour'] = $this->getStartHour();
        $data['endHour'] = $this->getEndHour();
        $data['line'] = $this->getLine();
        return $data;
    }
}