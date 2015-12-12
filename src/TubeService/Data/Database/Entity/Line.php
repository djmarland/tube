<?php
namespace TubeService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="tube_lines")})
 */
class Line extends Entity
{
    /** @ORM\Column(type="string", length=255) */
    private $url_key;

    /** @ORM\Column(type="string", length=255) */
    private $tfl_key;

    /** @ORM\Column(type="string", length=255) */
    private $name;

    /** @ORM\Column(type="string", length=255) */
    private $short_name;

    /** @ORM\Column(type="integer") */
    private $display_order;

    /** @ORM\ManyToOne(targetEntity="Status") */
    private $latest_status;

    /** Getters/Setters */
    public function getURLKey()
    {
        return $this->url_key;
    }

    public function setUrlKey($url_key)
    {
        $this->url_key = $url_key;
    }

    public function getTFLKey()
    {
        return $this->tfl_key;
    }

    public function setTFLKey($tfl_key)
    {
        $this->tfl_key = $tfl_key;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getShortName()
    {
        return $this->short_name;
    }

    public function setShortName($short_name)
    {
        $this->short_name = $short_name;
    }

    public function getDisplayOrder()
    {
        return $this->display_order;
    }

    public function setDisplayOrder($display_order)
    {
        $this->display_order = $display_order;
    }

    public function getLatestStatus()
    {
        return $this->latest_status;
    }

    public function setLatestStatus($latest_status)
    {
        $this->latest_status = $latest_status;
    }
}