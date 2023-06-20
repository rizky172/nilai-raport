<?php
namespace App\Libs\Repository;

use Illuminate\Support\Facades\Validator;

use App\Libs\Repository\AbstractRepository;

use App\Models\Category;
use App\Models\Person as Model;

class Person extends AbstractRepository
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    private function validateBasic()
    {
        $id = $this->model->id;

        $fields = [
            'ref_no' => $this->model->ref_no,
            'name' => $this->model->name,
            'email' => $this->model->email,
            'notes' => $this->model->notes,
        ];

        $rules = [
            'ref_no' => 'required|unique:person,ref_no,'.$id,
            'name' => 'required|max:128',
            'email' => "required|email|unique:person,email,$id",
            'notes' => 'nullable|max:255'
        ];

        $validator = Validator::make($fields, $rules);
        self::validOrThrow($validator);
    }

    public function validate()
    {
        $this->validateBasic();
    }

    protected function getPostfix()
    {
        return sprintf('/%s.%s', date('m'), date('y'));
    }

    public function beforeSave()
    {
        parent::beforeSave();
        $category = Category::find($this->model->person_category_id);

        // Generate new ref no if empty
        if(empty($this->model->ref_no)) {
            $this->model->ref_no = $this->generateRefNo(4, $this->getPrefix(), $this->getPostfix());
        }
    }

    public function toArray()
    {
        $data = $this->model->toArray();
        // $data['category'] = Category::find($this->model->person_category_id)->name;

        return $data;
    }
}
