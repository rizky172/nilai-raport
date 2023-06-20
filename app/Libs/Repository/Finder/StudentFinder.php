<?php
namespace App\Libs\Repository\Finder;

use App\Models\Person as Model;

class StudentFinder extends AbstractFinder
{
    public function __construct()
    {
        parent::__construct();

        $this->query = Model::select(
            'person.*',
            'major.label as major',
            'class.label as class'
        );

        $this->query->join('category', 'category.id', '=', 'person.person_category_id');
        $this->query->join('category as major', 'major.id', '=', 'person.major_id');
        $this->query->leftjoin('meta', function($join) {
            $join->on('meta.fk_id', 'person.id')
                 ->where('meta.table_name', 'person')
                 ->where('meta.key', 'class_id');
        });
        $this->query->join('category as class', 'class.id', '=', 'meta.value');
    }

    public function setOnlyTrashed($isDeleted)
    {
        if ($isDeleted == 1){
            $this->query->onlyTrashed();
        }
    }

    public function setWithTrashed($withTrashed)
    {
        if ($withTrashed == 1) {
            $this->query->withTrashed();
        }
    }

    public function orderBy($columnName, $orderBy)
    {
        switch($columnName) {
            case 'ref_no':
                $this->query->orderBy('person.ref_no', $orderBy);
                break;
        }
    }

    private function whereKeyword()
    {
        $keyword = $this->keyword;

        if(!empty($keyword)) {
            $list = explode(' ', $keyword);
            $list = array_map('trim', $list);

            $this->query->where(function($query) use ($list) {
                foreach($list as $x) {
                    $pattern = '%' . $x . '%';
                    $query->orWhere('person.ref_no', 'like', $pattern);
                    $query->orWhere('person.name', 'like', $pattern);
                    $query->orWhere('person.email', 'like', $pattern);
                    $query->orWhere('person.nis', 'like', $pattern);
                    $query->orWhere('class.label', 'like', $pattern);
                    $query->orWhere('major.label', 'like', $pattern);
                }
            });
        }
    }

    private function whereCategory()
    {
        $this->query->where('category.name', 'student');
    }

    private function filterByRole()
    {
        if (!$this->accessControl->hasAccess('student_full_access')) {
            $this->accessControl->hasPerson();
            $person = $this->accessControl->getUser();

            if (!empty($person)) {
                $this->query->where('person.id', $person->person_id);
            }
        }
    }

    public function doQuery()
    {
        $this->filterByAccessControl('student_read');
        $this->filterByRole();

        $this->whereCategory();
        $this->whereKeyword();
    }
}
