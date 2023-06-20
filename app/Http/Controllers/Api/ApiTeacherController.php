<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Libs\Repository\Finder\TeacherFinder;
use App\Libs\Repository\Teacher;

use App\Models\Person as Model;

class ApiTeacherController extends ApiController
{
    public function index(Request $request)
    {
        $finder = new TeacherFinder();
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
                'email' => $x->email,
                'nip' => $x->nip,
            ];
        }

        $this->jsonResponse->setData($data);
        $this->jsonResponse->setMeta($this->jsonResponse->getPaginatorConfig($paginator));

        return $this->jsonResponse->getResponse();
    }

    public function store(Request $request)
    {
        $row = Model::findOrNew($request->id);
        $row->ref_no = $request->ref_no;
        $row->nip = $request->nip;
        $row->name = $request->name;
        $row->email = $request->email;
        $row->address = $request->address;

        $repo = new Teacher($row);
        $repo->setAccessControl($this->getAccessControl());

        if (!empty($request->classId)) {
            foreach ($request->classId as $x) {
                $repo->addClass($x);
            }
        }

        if (!empty($request->majorId)) {
            foreach ($request->majorId as $x) {
                $repo->addMajor($x);
            }
        }

        if (!empty($request->lessonId)) {
            foreach ($request->lessonId as $x) {
                $repo->addLesson($x);
            }
        }

        $repo->setUser($request->username, $request->password, $request->retype_password);

        $repo->save();

        $this->jsonResponse->setMessage('Guru berhasil disimpan.');
        $this->jsonResponse->setData($row->id);

        return $this->jsonResponse->getResponse();
    }

    public function show($id)
    {
        $row = $this->getModel($id);
        $repo = new Teacher($row);
        $repo->setAccessControl($this->getAccessControl());

        $this->jsonResponse->setData($repo->toArray());

        return $this->jsonResponse->getResponse();
    }

    public function destroy($id, $permanent = null)
    {
        $row = Model::withTrashed()
            ->find($id);

        $repo = new Teacher($row);
        $repo->setAccessControl($this->getAccessControl());
        $repo->delete($permanent);

        $this->jsonResponse->setMessage('Guru berhasil dihapus.');

        return $this->jsonResponse->getResponse();
    }

    public function restore($id)
    {
        $row = Model::withTrashed()
            ->find($id);

        $repo = new Teacher($row);
        $repo->restore();

        $this->jsonResponse->setMessage('Guru berhasil dikembalikan.');

        return $this->jsonResponse->getResponse();
    }

    private function getModel($id)
    {
        $row = Model::find($id);
        if (empty($row)) {
            throw new NotFoundHttpException('Guru tidak ditemukan.');
        }

        return $row;
    }
}
