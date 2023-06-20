<?php
namespace App\Libs\Repository\Finder;

use App\Models\ReportValueDetail as Model;

class ReportReportValueFinder extends AbstractFinder
{
    private $studentId;

    public function __construct()
    {
        parent::__construct();

        $this->query = Model::select(
            'report_value_detail.value as nilai',
            'class.label as class',
            'major.label as major',
            'lesson.label as lesson',
            'semester.label as semester'
        );

        $this->query->join('report_value', 'report_value.id', '=', 'report_value_detail.report_value_id');
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

    public function orderBy($columnName, $orderBy){}

    private function whereKeyword(){}

    public function whereStudent()
    {
        $this->query->where('report_value_detail.student_id', $this->studentId);
    }

    public function doQuery()
    {
        $this->whereStudent();
    }
}
