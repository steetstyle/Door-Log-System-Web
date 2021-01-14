<?php 

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class CollectionExport implements FromCollection
{
    protected $datas;

    public function __construct($datas)
    {
        $this->datas = $datas;
    }

    public function collection()
    {
        return $this->datas;
    }
}