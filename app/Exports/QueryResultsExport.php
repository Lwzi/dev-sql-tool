<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QueryResultsExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(
        private readonly Collection $rows
    ) {
        //
    }

    public function collection(): Collection
    {
        if ($this->rows->isEmpty()) {
            return collect();
        }

        $headings = $this->headings();

        return $this->rows->map(function (array $row) use ($headings) {
            return collect($headings)
                ->map(fn (string $heading) => $row[$heading] ?? null)
                ->all();
        });
    }

    public function headings(): array
    {
        if ($this->rows->isEmpty()) {
            return [];
        }

        return array_keys($this->rows->first());
    }
}
