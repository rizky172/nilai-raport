<?php
namespace App\Libs\Generator;

use DB;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Carbon\Carbon;

use App\WevelopeLibs\Interfaces\IRepository;
use App\WevelopeLibs\Helper\DateFormat;
use App\Libs\Repository\AbstractRepository;

use App\Libs\Repository\Finder\CategorySalarySlipDetailFinder;
use App\Libs\Repository\SalarySlip as SalarySlipRepo;
use App\Libs\Repository\Person as PersonRepo;

use App\Invoice;
use App\Category;
use App\Person;

class SalarySlipGenerator extends AbstractRepository
{
    private $date;
    private $jenisId;
    private $employees;
    private $salarySlipCategory;

    // Otomatis generate semua invoice
    public function __construct(\Datetime $date, $jenisId = null)
    {
        $this->date = $date;
        $this->jenisId = $jenisId;

        // Get all customer that active
        $this->employees = Person::select('person.*')
                                ->join('category', 'category.id', '=', 'person.person_category_id')
                                ->where('category.name', 'employee')
                                ->where('person.is_active', 1)
                                ->get();

        $this->salarySlipCategory = Category::where('group_by', 'salary_slip')->get();
    }

    public function generate()
    {
        DB::beginTransaction();

        try {
            // // Get all customer that active
            // $employees = DB::table('person')
            //                         ->select('person.id', 'type.name as category', 'invoice.created')
            //                         ->join('category as person_category', 'person_category.id', '=', 'person.person_category_id')
            //                         ->leftjoin('invoice', 'invoice.person_id', '=', 'person.id')
            //                         ->leftjoin('category as invoice_category', 'invoice_category.id', '=', 'invoice.invoice_category_id')
            //                         ->leftjoin('meta', function ($join) {
            //                             $join->on('meta.table_name', '=', DB::raw("'invoice'"));
            //                             $join->on('meta.key', '=', DB::raw("'salary_slip_category_id'"));
            //                             $join->on('meta.fk_id', '=', "invoice.id");
            //                         })
            //                         ->leftjoin('category as type', 'type.id', '=', 'meta.value')
            //                         ->where('person_category.name', 'employee')
            //                         ->get();

            // foreach ($employees as $c => $cValue) {
            //     $employee = Person::find($cValue->id);

            //     $firstDate = clone $this->date;
            //     $firstDate->modify('first day of this month');

            //     $endDate = clone $this->date;
            //     $endDate->modify('last day of this month');

            //     $invoice = $employees->where('id', $cValue->id)
            //                             ->where('created', '>=', $firstDate->format('Y-m-d'))
            //                             ->where('created', '<=', $endDate->format('Y-m-d'));

            //     $bonus = $invoice->where('category', 'bonus')
            //                         ->first();

            //     $thr = $invoice->where('category', 'thr')
            //                         ->first();

            //     $gaji = $invoice->where('category', 'gaji')
            //                         ->first();

            //     var_dump($gaji);

            //     //check if invoice exist
            //     if (empty($bonus))
            //         $this->createSalarySlip('bonus', $employee);

            //     if (empty($thr))
            //         $this->createSalarySlip('thr', $employee);

            //     if (empty($gaji))
            //         $this->createSalarySlip('gaji', $employee);
            // }

            $this->validate();

            $employees = $this->employees;
            foreach ($employees as $c => $cValue) {
                $cRepo = new PersonRepo($employees[$c]);
                $employee = $cRepo->toArray();

                $invoices = Invoice::select('invoice.id', 'type.name as category', 'meta.value as salary_slip_category_id')
                                    ->join('category', 'category.id', '=', 'invoice.invoice_category_id')
                                    ->join('meta', function ($join) {
                                        $join->on('meta.table_name', '=', DB::raw("'invoice'"));
                                        $join->on('meta.key', '=', DB::raw("'salary_slip_category_id'"));
                                        $join->on('meta.fk_id', '=', "invoice.id");
                                    })
                                    ->join('category as type', 'type.id', '=', 'meta.value')
                                    ->where('invoice.person_id', $cValue['id'])
                                    ->whereMonth('invoice.created', $this->date->format('m'))
                                    ->whereYear('invoice.created', $this->date->format('Y'))
                                    ->get();

                $bonus = $invoices->where('category', 'bonus')
                                    ->first();

                $thr = $invoices->where('category', 'thr')
                                    ->first();

                $gaji = $invoices->where('category', 'gaji')
                                    ->first();

                $bonusId = $this->salarySlipCategory->where('name', 'bonus')->first()->id;
                $thrId = $this->salarySlipCategory->where('name', 'thr')->first()->id;
                $gajiId = $this->salarySlipCategory->where('name', 'gaji')->first()->id;

                //check if invoice exist
                if (empty($bonus) && ($this->jenisId == $bonusId || $this->jenisId == null) ) {
                    $this->createSalarySlip('bonus', $employees[$c]);
                }

                if (empty($thr) && ($this->jenisId == $thrId || $this->jenisId == null) ) {
                    $this->createSalarySlip('thr', $employees[$c]);
                }

                if (empty($gaji) && ($this->jenisId == $gajiId || $this->jenisId == null) ) {
                    $this->createSalarySlip('gaji', $employees[$c]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            $messages = $e->getMessage();

            if($e instanceof ValidationException)
                $messages = $e->validator->errors()->first();

            throw new \Exception($messages);
        }
    }

    private function validateSalarySlipDetail()
    {
        $salarySlipFinder = $finder = new CategorySalarySlipDetailFinder();
        $paginator = $finder->get();

        $coa = [];
        foreach ($paginator as $x) {
            $coa[] = [
                'id' => $x->id,
                'name' => $x->label,
                'coa_id_debit' => $x->coa_id_debit,
                'coa_id_credit' => $x->coa_id_credit,
            ];
        }

        $fields = [
            'coa' => $coa
        ];

        $rules = [
            'coa.*.coa_id_debit' => 'required',
            'coa.*.coa_id_credit' => 'required'
        ];

        $messages = [];

        foreach ($coa as $key => $value) {
            $messages[sprintf('coa.%s.coa_id_debit.required', $key)] = sprintf('Slip Gaji > Jenis Pembayaran=%s, COA Debit wajib diisi', $value['name']);
            $messages[sprintf('coa.%s.coa_id_credit.required', $key)] = sprintf('Slip Gaji > Jenis Pembayaran=%s, COA Kredit wajib diisi', $value['name']);
        }

        $validator = Validator::make($fields, $rules, $messages);
        AbstractRepository::validOrThrow($validator);
    }

    public function validate()
    {
        $this->validatePerson();
        $this->validateSalarySlipDetail();
    }

    private function validatePerson()
    {
        $fields = [];
        $rules = [];
        $messages = [];

        $employees = $this->employees->toArray();
        foreach ($employees as $key => $value) {
            $fields[sprintf('employe_marital_status_id_%s', $key)] = $value['marital_status_id'];
            $rules[sprintf('employe_marital_status_id_%s', $key)] = 'required|exists:category,id';
            $messages[sprintf('employe_marital_status_id_%s.required', $key)] = sprintf('Marital status %s wajib diisi', $value['name']);
        }

        $validator = Validator::make($fields, $rules, $messages);
        AbstractRepository::validOrThrow($validator);
    }

    /*
    * @array $person
    */
    private function createSalarySlip($type, $person)
    {
        $category = Category::where('name', $type)
                                ->where('group_by', 'salary_slip')
                                ->first();

        $row = new Invoice;
        $row->person_id = $person->id;
        $row->sent = 0;
        $row->created = $this->date;

        $repo = new SalarySlipRepo($row);
        $repo->setAccessControl($this->getAccessControl());
        $repo->setCategory($category->id);

        $detail = SalarySlipRepo::generateDetail($person->id, $this->date->format('Y-m-d'), $category->id);

        foreach ($detail['detail'] as $x) {
            $loan_id = empty($x['loan_id']) ? null : $x['loan_id'];

            $repo->addDetail(null, $x['invoice_detail_category_id'], $x['notes'], $x['qty'], $x['price'], $x['position'], $loan_id);
        }

        $repo->save();
    }
}
