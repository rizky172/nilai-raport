<?php
namespace App\Libs\ImportExport;

use Illuminate\Validation\Validator;
use Illuminate\Validation\ValidationException;

use Illuminate\Database\Eloquent\Model;
use Excel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use App\WevelopeLibs\AbstractExport;

use App\Libs\ImportExport\Sheet\CustomerListSheet;

class CustomerExport extends AbstractExport implements WithMultipleSheets
{
    private $keyword;
    private $filename;

    public function __construct() {
        $date = new \Datetime;
        $this->filename = 'customer_export_' . $date->format('Ymdhis') . '.xls';
    }

    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function validate()
    {
        return;
    }

    public function getData()
    {
        return;
    }

    public function collection()
    {
        return collect($this->getData());
    }

    public function headings() : array
    {
        return [];
    }

    public function sheets(): array {
        $filter = [];

        if (isset($this->keyword)) {
            $filter['keyword'] = $this->keyword;
        }


        $sheet = new CustomerListSheet($filter);
        $sheet->setAccessControl($this->getAccessControl());

        return [
            $sheet
        ];
    }

    public function setPermission()
    {
        $this->filterByAccessControl('customer_read');
    }

}
