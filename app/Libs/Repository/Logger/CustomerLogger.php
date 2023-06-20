<?php

namespace App\Libs\Repository\Logger;

use Wevelope\Wevelope\Parser;

use App\Category;
use App\Person;

class CustomerLogger extends AbstractLogger
{
    public function setup()
    {
        parent::setup();

        $this->updateRenameIndexArray([
            'coa_id' => 'COA',
            'sales_id' => 'Sales',
            'industri_category_id' => 'Industri',
            'billing_address' => 'Alamat Penagihan',
            'billing_period' => 'TOP',
            'factory' => 'Factory',
            'collector_name' => 'Nama Penagihan(PIC)',
            'city' => 'Kota',
            'npwp' => 'NPWP',
            'person_id' => 'Pusat',
            'is_ppn' => 'PPN'
        ]);

        $categoryList = Category::all();
        $mapper = $categoryList->map(function($x) {
            $value = $x['label'];
            return ['id' => $x['id'], 'value' => $value];
        });

        $parser = new Parser\IdToStringParser($mapper->toArray());
        $this->addParser('coa_id', $parser);

        $parser = new Parser\IdToStringParser($mapper->toArray());
        $this->addParser('industri_category_id', $parser);

        $personList = Person::all();
        $mapper = $personList->map(function($x) {
            $value = $x['name'];
            return ['id' => $x['id'], 'value' => $value];
        });

        $parser = new Parser\IdToStringParser($mapper->toArray());
        $this->addParser('sales_id', $parser);

        $parser = new Parser\IdToStringParser($mapper->toArray());
        $this->addParser('person_id', $parser);
    }
}
