<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Libs\Repository\Finder\StudentFinder;
use App\Libs\Repository\Student;

use App\Models\Person as Model;

class ApiStudentController extends ApiController
{
    public function index(Request $request)
    {
        $finder = new StudentFinder();
        $finder->setAccessControl($this->getAccessControl());

        if (isset($request->with_trashed)) {
            $finder->setWithTrashed($request->with_trashed);
        }

        if (isset($request->deleted)) {
            $finder->setOnlyTrashed($request->deleted);
        }

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
                'name' => $x->name,
                'nis' => $x->nis,
                'major' => $x->major,
                'class' => $x->class,
                'text' => sprintf('%s | %s | %s | %s', $x->nis, $x->name, $x->class, $x->major)
            ];
        }

        $this->jsonResponse->setData($data);
        $this->jsonResponse->setMeta($this->jsonResponse->getPaginatorConfig($paginator));

        return $this->jsonResponse->getResponse();
    }

    public function store(Request $request)
    {
        $row = Model::findOrNew($request->id);
        $row->major_id = $request->major_id;
        $row->ref_no = $request->ref_no;
        $row->nis = $request->nis;
        $row->name = $request->name;
        $row->email = $request->email;
        $row->phone = $request->phone;
        $row->address = $request->address;

        $repo = new Student($row);
        $repo->setAccessControl($this->getAccessControl());

        if (!empty($request->class_id)) {
            $repo->addClass($request->class_id);        
        }

        $repo->setUser($request->username, $request->password, $request->retype_password);

        $repo->save();

        $this->jsonResponse->setMessage('Siswa berhasil disimpan.');
        $this->jsonResponse->setData($row->id);

        return $this->jsonResponse->getResponse();
    }

    public function show($id)
    {
        $row = $this->getModel($id);
        $repo = new Student($row);
        $repo->setAccessControl($this->getAccessControl());

        $this->jsonResponse->setData($repo->toArray());

        return $this->jsonResponse->getResponse();
    }

    public function destroy($id, $permanent = null)
    {
        $row = Model::withTrashed()
            ->find($id);

        $repo = new Student($row);
        $repo->setAccessControl($this->getAccessControl());
        $repo->delete($permanent);

        $this->jsonResponse->setMessage('Siswa berhasil dihapus.');

        return $this->jsonResponse->getResponse();
    }

    public function restore($id)
    {
        $row = Model::withTrashed()
            ->find($id);

        $repo = new Student($row);
        $repo->restore();

        $this->jsonResponse->setMessage('Siswa berhasil dikembalikan.');

        return $this->jsonResponse->getResponse();
    }

    private function getModel($id)
    {
        $row = Model::find($id);
        if (empty($row)) {
            throw new NotFoundHttpException('Siswa tidak ditemukan.');
        }

        return $row;
    }

    // public function export(Request $request)
    // {
    //     $export = new StudentExport();
    //     $export->setAccessControl($this->getAccessControl());

    //     if ($request->keyword)
    //         $export->setKeyword($request->keyword);

    //     $export->run();
    //     $filename = $export->getFilename();

    //     // Generate URl and pass to json
    //     $this->jsonResponse->setData(['url' => ResourceUrl::tmp($filename)]);
    //     return $this->jsonResponse->getResponse();
    // }

    // public function import(Request $request)
    // {
    //     $import = new StudentImport($request->file('file'));
    //     $import->setAccessControl($this->getAccessControl());
    //     $import->run();

    //     $message = $import->getErrorMessage();
    //     if(empty($message)) {
    //         $message = 'Import berhasil.';
    //     }

    //     $this->jsonResponse->setMessage($message);
    //     return $this->jsonResponse->getResponse();
    // }
}
