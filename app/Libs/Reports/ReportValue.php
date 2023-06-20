<?php 

namespace App\Libs\Reports;

use App\Libs\Repository\Finder\ReportReportValueFinder;

class ReportValue extends ReportReportValueFinder
{
    public function getData()
    {
        $this->setPage('all');
        $temp = $this->get();

        $data = [];
        foreach ($temp as $key =>$value) {
            $data[] = [
                'value' => $value->nilai,
                'class' => $value->class,
                'major' => $value->major,
                'lesson' => $value->lesson,
                'semester' => $value->semester
            ];
        }

        return $data;
    }

    public static function createFromFinder(ReportReportValueFinder $finder)
    {
        $data = new self();
        $data->setAccessControl($finder->getAccessControl());

        $param = $finder->getStudent();
        if (!empty($param))
            $data->setStudent($param);

        return $data;
    }
}