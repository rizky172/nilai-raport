<?php

namespace App\Libs\ImportExport;

use Excel;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

use App\WevelopeLibs\AbstractImport;

use App\Libs\Libs\Remap;
use App\Libs\Libs\ParseStrategy\StringToArrayStrategy;
use App\Libs\Repository\AbstractRepository;

use App\Libs\ImportExport\Sheet\CustomerListSheet;
use App\Libs\Repository\Customer;

use App\Models\Person as Model;
use App\Models\Category;

class CustomerImport extends AbstractImport
{
    private $list;
    private $errorRowList = []; // Contain ID row that failed to insert

    public function headingRow()
    {
        return 1;
    }

    // Validate plain data
    public function validateData()
    {
        $list = $this->list->toArray();

        $fields = [
            'customer' => $list
        ];

        $rules = [
            'customer.*.ref_no' => 'required|distinct',
            'customer.*.name' => 'required',
            'customer.*.email' => 'required|email|distinct',
            'customer.*.tier' => [
                'required',
                Rule::exists('category', 'label')
                    ->where('group_by', 'tier')
            ],
        ];

        $messages = [];

        foreach ($list as $x => $val) {
            $row = $x + 1;

            $messages[sprintf('customer.%s.ref_no.distinct', $x)] = sprintf('No. Ref pada excel baris ke %s kembar.', $row);

            $messages[sprintf('customer.%s.ref_no.required', $x)] = sprintf('No. Ref pada excel baris ke %s wajib diisi.', $row);
            $messages[sprintf('customer.%s.name.required', $x)] = sprintf('PIC pada excel baris ke %s wajib diisi.', $row);

            $messages[sprintf('customer.%s.email.required', $x)] = sprintf('Email pada excel baris ke %s wajib diisi.', $row);
            $messages[sprintf('customer.%s.email.email', $x)] = sprintf('Email pada excel baris ke %s tidak valid.', $row);
            $messages[sprintf('customer.%s.email.distinct', $x)] = sprintf('Email pada excel baris ke %s kembar.', $row);

            $messages[sprintf('customer.%s.tier.required', $x)] = sprintf('Tier pada excel baris ke %s wajib diisi.', $row);
            $messages[sprintf('customer.%s.tier.exists', $x)] = sprintf('Tier pada excel baris ke %s tidak valid.', $row);
        }

        $validator = Validator::make($fields, $rules, $messages);
        AbstractRepository::validOrThrow($validator);
    }

    public function validate()
    {
        $this->validateData();
    }

    public function run()
    {
        $this->filterByAccessControl('customer_create');

        $list = Excel::toArray(new CustomerListSheet, $this->file); // used for convert file to array
        $this->list = self::remap($list[0]); // For validate, remap header to column name

        $this->validate();

        $list = $this->list->toArray();
        for ($i=0; $i<count($list); $i++) {
            $x = $list[$i];

            $x = (object) $x;

            $model = null;

            // Check REF_NO
            $exists = Model::where('ref_no', $x->ref_no)->first();
            if (!empty($exists)) {
                $model = Model::find($exists->id);
            }

            // If model still empty then create new
            if(empty($model)){
                $model = new Model;
            }

            $tier = Category::where('group_by', 'tier')
                            ->where('label', $x->tier)
                            ->first();

            $model->tier_id = $tier->id;
            $model->ref_no = $x->ref_no;
            $model->name = $x->name;
            $model->email = $x->email;
            $model->company_name = $x->company_name;
            $model->group = $x->group;
            $model->npwp = $x->npwp;
            $model->notes = $x->notes;
            $model->due_date = $x->due_date;

            $repo = new Customer($model);
            $repo->save();
        }
    }

    public function getErrorMessage()
    {
        $message = null;
        if(!empty($this->errorRowList)) {
            $message = 'Kolom :list-kolom: pada excel baris :list-baris: wajib di isi.';
            $message = str_replace(':list-baris:', implode(', ', $this->errorRowList), $message);
        }

        return $message;
    }

    // Remap column
    private static function remap($list)
    {
        // Must be in order
        $columns = explode(',', 'ref_no,name,email,company_name,group,npwp,tier,due_date,notes');

        $stringToArray = new StringToArrayStrategy();

        // Remove header
        array_shift($list);
        $remap = new Remap($columns, $list);
        $newList = $remap->getNewList();

        return collect($newList);
    }
}
