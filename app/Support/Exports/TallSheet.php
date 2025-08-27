<?php

namespace App\Support\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TallSheet implements FromArray, WithTitle, WithHeadings
{
    public function __construct(private string $title, private array $rows) {}

    public function title(): string { return $this->title; }

    public function headings(): array { return ['month','department','count']; }

    public function array(): array
    {
        // Ensure consistent order: month asc, then dept asc
        usort($this->rows, function($a,$b){
            return [$a['month'],$a['department']] <=> [$b['month'],$b['department']];
        });
        return array_map(fn($r)=>[$r['month'],$r['department'],$r['count']], $this->rows);
    }
}
