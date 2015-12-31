<?php
namespace TubeService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="subscriptions")})
 */
class Subscription extends Entity
{

    /** @ORM\ManyToOne(targetEntity="Line") */
    private $line;

    /** @ORM\Column(type="boolean", options={"default":1}) */
    private $is_active = 1;

    /** @ORM\Column(type="integer") */
    private $day;

    /** @ORM\Column(type="integer") */
    private $start_hour;

    /** @ORM\Column(type="integer") */
    private $end_hour;

    /** @ORM\Column(type="string", length=10000) */
    private $endpoint;


    public function getLine()
    {
        return $this->line;
    }
    public function setLine($line)
    {
        $this->line = $line;
    }

    public function getIsActive()
    {
        return $this->is_active;
    }
    public function setIsActive($is_active)
    {
        $this->is_active = $is_active;
    }

    public function getDay()
    {
        return $this->day;
    }
    public function setDay($day)
    {
        $this->day = $day;
    }

    public function getStartHour()
    {
        return $this->start_hour;
    }
    public function setStartHour($start_hour)
    {
        $this->start_hour = $start_hour;
    }

    public function getEndHour()
    {
        return $this->end_hour;
    }
    public function setEndHour($end_hour)
    {
        $this->end_hour = $end_hour;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }
}