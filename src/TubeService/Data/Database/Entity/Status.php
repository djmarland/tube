<?php
namespace TubeService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="status")})
 */
class Status extends Entity
{

    /** @ORM\ManyToOne(targetEntity="Line") */
    private $line;

    /** @ORM\Column(type="boolean") */
    private $is_disrupted;

    /** @ORM\Column(type="string", length=255) */
    private $short_title;

    /** @ORM\Column(type="string", length=1000) */
    private $title;

    /** @ORM\Column(type="string", length=10000, nullable=true) */
    private $description;


    public function getLine()
    {
        return $this->line;
    }
    public function setLine($line)
    {
        $this->line = $line;
    }

    public function getIsDisrupted()
    {
        return $this->is_disrupted;
    }
    public function setIsDisrupted($is_disrupted)
    {
        $this->is_disrupted = $is_disrupted;
    }

    public function getShortTitle()
    {
        return $this->short_title;
    }
    public function setShortTitle($short_title)
    {
        $this->short_title = $short_title;
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
}