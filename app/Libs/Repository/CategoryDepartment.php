<?php
namespace App\Libs\Repository;

use Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\WevelopeLibs\Helper\DateFormat;
use App\Libs\Repository\Category;

use App\Category as Model;

class CategoryDepartment extends Category
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    public function validateCategory()
    {
        $accepted = ['department'];

        if (!in_array($this->model->group_by, $accepted)) {
            throw new \Exception("Category tidak ada");
        }


        if (!empty($this->details) && $this->model->group_by == 'item') {
            $this->model->category_id = Model::where('group_by', 'item')
                                                ->where('name', 'other')
                                                ->first()
                                                ->id;
        }

        // Validation
        $fields = [
            'label' => $this->model->label,
            'name' => $this->model->name,
            'notes' => $this->model->notes,
            'group_by' => $this->model->group_by
        ];

        $rules = [
            'name' => [
                'numeric', 
                'digits_between:2,2',
                Rule::unique('category')->where(function ($query) {
                    return $query->where('name', $this->model->name)
                    ->where('id', '!=', $this->model->id)
                    ->where('group_by', $this->model->group_by);
                }),
            ],
            'label' => [
                'required',
                'max:45',
                Rule::unique('category')->where(function ($query) {
                    return $query->where('name', $this->model->name)
                    ->where('id', '!=', $this->model->id)
                    ->where('group_by', $this->model->group_by);
                }),
            ],
            'group_by' => 'required'
        ];

        $message = [
            'name.numeric' => 'Kode harus berupa angka',
            'name.digits_between' => 'Kode harus berisi 2 digit.',
            'name.unique' => 'Kode sudah terpakai.'
        ];

        $validator = Validator::make($fields, $rules, $message);
        self::validOrThrow($validator);
    }

    private function formatCodeDepartment() {
        if(strlen(strval($this->model->name)) < 2) {
            $this->model->name =  sprintf("%02d", $this->model->name);
        }

        info($this->model->name);
    }

    public function save()
    {
        $this->formatCodeDepartment();
        parent::save();
    }
}
