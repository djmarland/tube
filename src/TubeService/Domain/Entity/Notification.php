<?php

namespace TubeService\Domain\Entity;

use TubeService\Domain\ValueObject\ID;
use DateTime;

class Notification extends Entity
{
    public function __construct(
        ID $id,
        DateTime $createdAt,
        DateTime $updatedAt,
        string $endpoint,
        string $title,
        string $description,
        string $url,
        string $image
    ) {
        parent::__construct(
            $id,
            $createdAt,
            $updatedAt
        );

        $this->endpoint = $endpoint;
        $this->title = $title;
        $this->description = $description;
        $this->url = $url;
        $this->image = $image;
    }

    private $endpoint;

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    private $title;

    public function getTitle()
    {
        return $this->title;
    }

    private $description;

    public function getDescription()
    {
        return $this->description;
    }

    private $url;

    public function getUrl()
    {
        return $this->url;
    }

    private $image;

    public function getImage()
    {
        return $this->image;
    }

    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['title'] = $this->getTitle();
        $data['description'] = $this->getDescription();
        $data['url'] = $this->getUrl();
        $data['image'] = $this->getImage();
        return $data;
    }
}