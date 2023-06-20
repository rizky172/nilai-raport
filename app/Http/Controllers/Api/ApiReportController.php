<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Libs\Repository\Finder\ReportReportValueFinder;
use App\Libs\Reports\ReportValue;

class ApiReportController extends ApiController
{
    public function reportValueIndex(Request $request)
    {
        $finder = new ReportReportValueFinder();
        $finder->setAccessControl($this->getAccessControl());

        if(isset($request->student_id))
            $finder->setStudent($request->student_id);

        $total = ReportValue::createFromFinder($finder);
        $data = $total->getData();

        $this->jsonResponse->setData($data);

        return $this->jsonResponse->getResponse();
    }
}
