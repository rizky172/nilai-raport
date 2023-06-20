<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Libs\Repository\Finder\ReportValueFinder;
use App\Libs\Repository\ReportValue;

use App\Models\ReportValue as Model;

class ApiReportValueController extends ApiController
{
    public function index(Request $request)
    {
        $finder = new ReportValueFinder();
        $finder->setAccessControl($this->getAccessControl());

        // Search by keyword
        if ($request->keyword)
            $finder->setKeyword($request->keyword);

        if ($request->page)
            $finder->setPage($request->page);

        if (isset($request->order_by) && !empty($request->order_by))
            $finder->orderBy($request->order_by['column'], $request->order_by['ordered']);

        $paginator = $finder->get();

        $data = [];
        foreach ($paginator as $x) {
            $data[] = [
                'id' => $x->id,
                'ref_no' => $x->ref_no,
                'teacher' => $x->teacher,
                'class' => $x->class,
                'major' => $x->major,
                'lesson' => $x->lesson,
                'semester' => $x->semester
            ];
        }

        $this->jsonResponse->setData($data);
        $this->jsonResponse->setMeta($this->jsonResponse->getPaginatorConfig($paginator));

        return $this->jsonResponse->getResponse();
    }

    public function store(Request $request)
    {
        $row = Model::findOrNew($request->id);
        $row->teacher_id = $request->teacher_id;
        $row->class_id = $request->class_id;
        $row->major_id = $request->major_id;
        $row->lesson_id = $request->lesson_id;
        $row->semester_id = $request->semester_id;
        $row->ref_no = $request->ref_no;

        $repo = new ReportValue($row);
        $repo->setAccessControl($this->getAccessControl());

        if (!empty($request->detail)) {
            foreach ($request->detail as $x) {
                $x = (array) $x;
                $id = empty($x['id']) ? null : $x['id'];

                $repo->addDetail(
                    $id,
                    $x['student_id'],
                    $x['value']
                );
            }
        }

        $repo->save();

        $this->jsonResponse->setMessage('Nilai Raport berhasil disimpan.');
        $this->jsonResponse->setData($row->id);

        return $this->jsonResponse->getResponse();
    }

    public function show($id)
    {
        $row = $this->getModel($id);

        $repo = new ReportValue($row);
        $repo->setAccessControl($this->getAccessControl());

        $this->jsonResponse->setData($repo->toArray());

        return $this->jsonResponse->getResponse();
    }

    public function destroy($id)
    {
        $row = $this->getModel($id);

        $repo = new ReportValue($row);
        $repo->setAccessControl($this->getAccessControl());
        $repo->delete();

        $this->jsonResponse->setMessage('Nilai Raport berhasil dihapus.');

        return $this->jsonResponse->getResponse();
    }

    private function getModel($id)
    {
        $row = Model::find($id);
        if (empty($row)) {
            throw new NotFoundHttpException('Nilai Raport tidak ditemukan.');
        }

        return $row;
    }

    public function getReportValue(Request $request)
    {
        $teacherId = $request->teacher_id;
        $classId = $request->class_id;
        $majorId = $request->major_id;

        $row = new Model();

        $repo = new ReportValue($row);
        $repo->setAccessControl($this->getAccessControl());

        $this->jsonResponse->setData($repo->getReportValue($teacherId, $classId, $majorId));
        return $this->jsonResponse->getResponse();        
    }
}
