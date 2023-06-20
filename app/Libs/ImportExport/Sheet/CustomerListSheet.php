<?php
namespace App\Libs\ImportExport\Sheet;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

use App\Libs\Repository\Finder\CustomerFinder;

class CustomerListSheet implements FromCollection, WithTitle, ShouldAutoSize
{
    private $filter;
    private $data = [];
    private $accessControl;

    public function __construct($filter = null) {
        $this->filter = $filter;

        $this->data[] = $this->headings();
    }

    public function title(): string
    {
        return 'list';
    }

    public function collection(): Collection
    {
        // Get data using finder
        $finder = new CustomerFinder();
        $finder->setAccessControl($this->accessControl);

        if(isset($this->filter['keyword']))
            $finder->setKeyword($this->filter['keyword']);

        $finder->setPage('all');
        $paginator = $finder->get();

        foreach($paginator as $x) {
            $this->data[] = [
                'REF_NO' => $x->ref_no,
                'PIC' => $x->name,
                'EMAIL' => $x->email,
                'NAMA_PERUSAHAAN' => $x->company_name,
                'GROUP' => $x->group,
                'NPWP' => $x->npwp,
                'TIER' => $x->tier,
                'TGL_JATUH_TEMPO' => $x->due_date,
                'CATATAN' => $x->notes
            ];
        }

        return collect($this->data);
    }

    private function headings() : array
    {
        return [
            'REF_NO',
            'PIC',
            'EMAIL',
            'NAMA_PERUSAHAAN',
            'GROUP',
            'NPWP',
            'TIER',
            'TGL_JATUH_TEMPO',
            'CATATAN'
        ];
    }

    public function setAccessControl($accessControl)
    {
        $this->accessControl = $accessControl;
    }
}
