<?php

namespace IngestService\Domain;

class TFLLine
{

    private $tflKey;
    private $statuses;

    public function __construct(
        string $tflKey,
        array $statuses
    ) {
        $this->tflKey = $tflKey;
        $this->statuses = $statuses;
    }

    public function getTFLKey()
    {
        return $this->tflKey;
    }

    public function getStatuses()
    {
        return $this->statuses;
    }

    public function isDisrupted()
    {
        foreach($this->getStatuses() as $status) {
            if ($status->isDisrupted()) {
                return true;
            }
        }
        return false;
    }

    public function getSortedStatuses()
    {
        $statuses = $this->getStatuses();
        usort($statuses, function($a, $b) {
            return $a->getDisplayOrder() <=> $b->getDisplayOrder(); // https://www.youtube.com/watch?v=7TYJyCCO8Dc
        });
        return $statuses;
    }

    private function getStatusTitles()
    {
        // get the ordered statuses
        $statuses = $this->getSortedStatuses();
        // get all of their titles
        $titles = array_map(function($s) {
            return $s->getTitle();
        }, $statuses);
        // remove duplications
        return array_unique($titles);
    }

    public function getStatusShortTitle()
    {
        $titles = $this->getStatusTitles();
        // grab the first two
        $titles = array_slice($titles, 0, 2);
        return implode(', ', $titles);
    }

    public function getStatusTitle()
    {
        $titles = $this->getStatusTitles();
        return implode(', ', $titles);
    }

    public function getStatusDescriptions()
    {
        // get the ordered statuses
        $statuses = $this->getSortedStatuses();
        // concatenate the descriptions
        $reasons = [];
        foreach($statuses as $s) {
            $reason = $s->getReason();
            if ($reason) {
                $reasons[] = $reason;
            }
        }
        if (!empty($reasons)) {
            return $reasons;
        }
        return null;
    }
}