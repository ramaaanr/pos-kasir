<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetReportExport implements WithMultipleSheets
{
    protected $sheets;

    public function __construct(array $sheets)
    {
        $this->sheets = $sheets;
    }

    public function sheets(): array
    {
        $sheetExports = [];
        foreach ($this->sheets as $title => $data) {
            $sheetExports[] = new ReportExport($data, $title);
        }
        return $sheetExports;
    }
}
