<?php
namespace App\Libs\Repository\Finder;

use App\Models\ReportValue as Model;

class ReportValueFinder extends AbstractFinder
{
    private $studentId;

    public function __construct()
    {
        parent::__construct();

        $this->query = Model::select(
            'report_value.*',
            'person.name as teacher',
            'class.label as class',
            'major.label as major',
            'lesson.label as lesson',
            'semester.label as semester'
        );

        $this->query->join('person', 'person.id', '=', 'report_value.teacher_id');
        $this->query->join('category as class', 'class.id', '=', 'report_value.class_id');
        $this->query->join('category as major', 'major.id', '=', 'report_value.major_id');
        $this->query->join('category as lesson', 'lesson.id', '=', 'report_value.lesson_id');
        $this->query->join('category as semester', 'semester.id', '=', 'report_value.semester_id');
    }

    public function setStudent($studentId)
    {
        $this->studentId = $studentId;
    }

    public function getStudent()
    {
        return $this->studentId;
    }

    public function orderBy($columnName, $orderBy)
    {
        switch($columnName) {
            case 'ref_no':
                $this->query->orderBy('report_value.ref_no', $orderBy);
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
                    $query->orWhere('report_value.ref_no', 'like', $pattern);
                }
            });
        }
    }

    private function filterByRole()
    {
        if (!$this->accessControl->hasAccess('report_value_full_access')) {
            $this->accessControl->hasPerson();
            $person = $this->accessControl->getUser();

            if (!empty($person)) {
                $this->query->where('report_value.teacher_id', $person->person_id);
            }
        }
    }

    public function doQuery()
    {
        $this->filterByAccessControl('report_value_read');
        $this->filterByRole();

        $this->whereKeyword();
    }
}
