<?php

namespace App\Support\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class MetaSheet implements FromArray, WithTitle
{
    public function __construct(private string $title, private array $meta) {}

    public function title(): string { return $this->title; }

    public function array(): array
    {
        $rows = [];
        foreach ($this->meta as $k => $v) {
            if (is_array($v)) $v = implode(', ', $v);
            $rows[] = [strtoupper((string)$k), (string)$v];
        }
        return $rows;
    }
}
