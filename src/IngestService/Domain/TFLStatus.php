<?php

namespace IngestService\Domain;

class TFLStatus
{
    const SEVERITIES = [
        1 => [
            'title' => 'Closed',
            'disrupted' => true,
            'displayOrder' => 1
        ],
        2 => [
            'title' => 'Suspended',
            'disrupted' => true,
            'displayOrder' => 1
        ],
        3 => [
            'title' => 'Part Suspended',
            'disrupted' => true,
            'displayOrder' => 1
        ],
        4 => [
            'title' => 'Planned Closure',
            'disrupted' => true,
            'displayOrder' => 1
        ],
        5 => [
            'title' => 'Part Closure',
            'disrupted' => true,
            'displayOrder' => 1
        ],
        6 => [
            'title' => 'Severe Delays',
            'disrupted' => true,
            'displayOrder' => 5
        ],
        7 => [
            'title' => 'Reduced Service',
            'disrupted' => true,
            'displayOrder' => 5
        ],
        8 => [
            'title' => 'Bus Service',
            'disrupted' => true,
            'displayOrder' => 5
        ],
        9 => [
            'title' => 'Minor Delays',
            'disrupted' => true,
            'displayOrder' => 10
        ],
        10 => [
            'title' => 'Good Service',
            'disrupted' => false,
            'displayOrder' => 100
        ],
        11 => [
            'title' => 'Part Closed',
            'disrupted' => true,
            'displayOrder' => 5
        ],
        12 => [
            'title' => 'Exist Only',
            'disrupted' => true,
            'displayOrder' => 20
        ],
        13 => [
            'title' => 'No Step Free Access',
            'disrupted' => true,
            'displayOrder' => 20
        ],
        14 => [
            'title' => 'Change of frequency',
            'disrupted' => true,
            'displayOrder' => 20
        ],
        15 => [
            'title' => 'Diverted',
            'disrupted' => true,
            'displayOrder' => 20
        ],
        16 => [
            'title' => 'Not Running',
            'disrupted' => true,
            'displayOrder' => 1
        ],
        17 => [
            'title' => 'Issues Reported',
            'disrupted' => true,
            'displayOrder' => 25
        ],
        18 => [
            'title' => 'No Issues',
            'disrupted' => false,
            'displayOrder' => 50
        ],
        19 => [
            'title' => 'Information',
            'disrupted' => false,
            'displayOrder' => 50
        ],
        20 => [
            'title' => 'Service Closed',
            'disrupted' => true,
            'displayOrder' => 1
        ]
    ];

    private $severity;
    private $title;
    private $reason;

    public function __construct(
        int $severity,
        string $title,
        string $reason = null
    ) {
        $this->severity = $severity;
        $this->title = $title;
        $this->reason = $reason;
    }

    public function getSeverity(): int
    {
        return $this->severity;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getReason()
    {
        return $this->reason;
    }

    public function getDisplayOrder(): int
    {
        return self::SEVERITIES[$this->getSeverity()]['displayOrder'] ?? 0;
    }

    public function isDisrupted(): bool
    {
        return self::SEVERITIES[$this->getSeverity()]['disrupted'] ?? true;
    }
}