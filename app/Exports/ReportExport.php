<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $data;
    protected $headings;
    protected $title;

    public function __construct(array $data, string $title = 'Report')
    {
        $this->title = $title;
        if (empty($data)) {
            $this->data = [];
            $this->headings = [];
            return;
        }
        $this->data = $data;
        $this->headings = array_keys($data[0]);
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
