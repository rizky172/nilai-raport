<?php

namespace App\Http\Controllers\Api;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Libs\Helpers\ResourceUrl;
use App\Libs\Repository\Finder\PersonFinder;
use App\Libs\Repository\Person;
use App\Libs\ImportExport\CustomerExport;
use App\Libs\ImportExport\CustomerImport;
use App\Libs\ImportExport\SupplierImport;

use App\Models\Person as Model;
use App\Models\Category;
use App\Models\Meta;

class ApiPersonController extends ApiController
{

    private function getCategoryPerson($isSupplier) {

        $cat = Category::where('group_by', 'person');

        if($isSupplier) {
            $cat->where('name', 'supplier');
        } else {
            $cat->where('name', 'customer');
        }

        return $cat->first();
    }

    public function index(Request $request)
    {
        $finder = new PersonFinder();

        if (isset($request->with_trashed)) {
            $finder->setWithTrashed($request->with_trashed);
        }

        if (isset($request->category)) {
            $finder->setCategory($request->category);
        }

        if(isset($request->deleted)) {
            $finder->setOnlyTrashed($request->deleted);
        }

        if(isset($request->include_id)) {
            $finder->setIncludeId($request->include_id);
        }

        // Search by keyword
        if($request->keyword)
            $finder->setKeyword($request->keyword);

        if (isset($request->is_supplier)) {
            $cat = $this->getCategoryPerson($request->is_supplier);

            if($cat) {
                $finder->setCategory($cat->name);
            }
        }

        if($request->page)
            $finder->setPage($request->page);

        if(isset($request->order_by) && !empty($request->order_by))
            $finder->orderBy($request->order_by['column'], $request->order_by['ordered']);

        // dd()
        $paginator = $finder->get();

        $data = [];
        foreach($paginator as $x){
            $person = $x->toArray();
            $person['sales'] = $x->sales_name;
            $person['text'] = sprintf('%s - %s - %s', $x->ref_no, $x->company_name, $x->name);
            $data[] = $person;
        }

        $this->jsonResponse->setData($data);
        $this->jsonResponse->setMeta($this->jsonResponse->getPaginatorConfig($paginator));

        return $this->jsonResponse->getResponse();
    }

    public function store(Request $request)
    {
        $row = Model::findOrNew($request->id);
        $row->ref_no = $request->ref_no;
        $row->name = $request->name;
        $row->company_name = $request->company_name;
        $row->billing_period = $request->billing_period;
        $row->email = $request->email;
        $row->address = $request->address;
        $row->notes = $request->notes;
        $row->person_category_id = Category::where('group_by', 'person')->where('name', $request->category)->first()->id;
        $row->city = $request->city;
        $row->npwp = $request->npwp;
        $row->is_ppn = $request->is_ppn;
        $row->person_id = $request->person_id;

        $repo = new Person($row);
        $repo->setAccessControl($this->getAccessControl());

        if (isset($request->sales_id)) {
            $repo->setSalesId($request->sales_id);
        }

        if (isset($request->billing_address)) {
            $repo->setBillingAddress($request->billing_address);
        }

        if (isset($request->factory)) {
            $repo->setFactory($request->factory);
        }

        if (!empty($request->phones)) {
            foreach ($request->phones as $x) {
                $repo->addPhones($x);
            }
        }

        if (!empty($request->fax)) {
            foreach ($request->fax as $x) {
                $repo->addFax($x);
            }
        }

        if ($request->_delete_account) {
            foreach ($request->_delete_account as $x) {
                $repo->deleteAccount($x);
            }
        }

        if (!empty($request->account)) {
            foreach ($request->account as $x) {
                $x = (array) $x;

                $id = empty($x['id']) ? null : $x['id'];

                $repo->addAccount($id, $x['bank_id'], $x['account_number']);
            }
        }

        if (isset($request->coa_id)) {
            $repo->setCoaId($request->coa_id);
        }

        if (isset($request->industri_category_id)) {
            $repo->setIndustriCategoryId($request->industri_category_id);
        }

        if (isset($request->collector_name)) {
            $repo->setCollectorName($request->collector_name);
        }

        $repo->save();

        $category = Category::find($row->person_category_id);
        $data = [
            'id' => $row->id,
            'category' => $category->name
        ];

        $this->jsonResponse->setMessage($category->label . ' telah berhasil tersimpan.');
        $this->jsonResponse->setData($data);

        return $this->jsonResponse->getResponse();
    }

    public function show($id)
    {
        $row = $this->getModel($id);
        $repo = new Person($row);

        $this->jsonResponse->setData($repo->toArray());

        return $this->jsonResponse->getResponse();
    }

    public function destroy($id, $permanent = null)
    {
        $row = Model::withTrashed()
                        ->find($id);

        $repo = new Person($row);
        $repo->delete($permanent);

        $this->jsonResponse->setMessage(Category::find($row->person_category_id)->label . ' telah berhasil dihapus.');

        return $this->jsonResponse->getResponse();
    }

    public function restore($id)
    {
        $row = Model::withTrashed()
                        ->find($id);

        $repo = new Person($row);
        $repo->restore();

        $this->jsonResponse->setMessage(Category::find($row->person_category_id)->label . ' berhasil dikembalikan');

        return $this->jsonResponse->getResponse();
    }

    private function getModel($id)
    {
        $row = Model::find($id);
        if(empty($row)){
            throw new NotFoundHttpException('Customer/Supplier tidak ditemukan');
        }

        return $row;
    }

    public function export(Request $request){
        $export = new CustomerExport();
        $export->setAccessControl($this->getAccessControl());
        $export->setCategory($request->category);

        if($request->keyword)
            $export->setKeyword($request->keyword);

        $export->run();
        $filename = $export->getFilename();

        // Generate URl and pass to json
        $this->jsonResponse->setData(['url' => ResourceUrl::tmp($filename)]);
        return $this->jsonResponse->getResponse();
    }

    public function import(Request $request)
    {
        $import = null;
        $data = json_decode($request->data);
        if ($data->category == "supplier") {
            $import = new SupplierImport($request->file('file'));
        } else {
            $import = new CustomerImport($request->file('file'));
        }
        $import->setAccessControl($this->getAccessControl());
        $import->run();

        $message = $import->getErrorMessage();
        if(empty($message)) {
            $message = 'Import telah berhasil.';
        }

        $this->jsonResponse->setMessage($message);
        return $this->jsonResponse->getResponse();
    }
}
