<?php

namespace App\Support\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OrgAnalyticsExport implements WithMultipleSheets
{
    public function __construct(private array $payload) {}

    public function sheets(): array
    {
        return [
            new TallSheet('Listings per dept',         $this->payload['listings'] ?? []),
            new TallSheet('Tasks per dept',            $this->payload['tasks'] ?? []),
            new TallSheet('Participants (accepted)',   $this->payload['participants_accepted'] ?? []),
            new TallSheet('Users (all, registered)',   $this->payload['users_all'] ?? []),
            new MetaSheet('Summary', $this->payload['meta'] ?? []),
        ];
    }
}
