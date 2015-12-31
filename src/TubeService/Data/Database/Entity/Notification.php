<?php
namespace TubeService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="notifications")})
 */
class Notification extends Entity
{

    /** @ORM\Column(type="boolean", options={"default":0}) */
    private $processed = 0;

    /** @ORM\Column(type="string", length=10000) */
    private $endpoint;

    /** @ORM\Column(type="string", length=100) */
    private $title;

    /** @ORM\Column(type="string", length=400) */
    private $description;

    /** @ORM\Column(type="string", length=400) */
    private $url;

    /** @ORM\Column(type="string", length=400) */
    private $image;

    public function getProcessed()
    {
        return $this->processed;
    }

    public function setProcessed($processed)
    {
        $this->processed = $processed;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }
}