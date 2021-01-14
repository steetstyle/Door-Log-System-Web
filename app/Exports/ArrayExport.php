<?php 

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ArrayExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $datas;
    protected $headings;
    protected $styles;


    public function __construct(array $datas, array $headings, array $styles)
    {
        $this->datas = $datas;
        $this->headings = $headings;
        $this->styles = $styles;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function array() :array
    {
        return $this->datas;
    }

    public function styles(Worksheet $sheet) :array
    {
        return $this->styles;
    }
}