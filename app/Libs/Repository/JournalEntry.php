<?php
namespace App\Libs\Repository;

use Hash;
use DB;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\WevelopeLibs\Helper\DateFormat;
use App\Libs\Libs\LogMaker;
use App\Libs\Meta\MetaManager;
use App\Libs\Meta\JournalEntryMetaConfig;
use App\Libs\Repository\AbstractRepository;
use App\Libs\Repository\Logger\JournalEntryLogger;
use App\Libs\Helpers\Position;

use App\JournalEntry as Model;
use App\JournalEntryDetail;
use App\GoodsReceived;
use App\PurchaseOrder;
use App\Category;
use App\Invoice;
use App\Meta;
use App\LogM;
use App\User;

class JournalEntry extends AbstractRepository
{
    private $details = [];
    private $deleted_details = [];
    private $originalDetail = [];
    private $businessCodeId;
    private $employeeId;
    private $departmentId;
    private $refNoPostfix;

    private $metaConfig;

    public function __construct(Model $model)
    {
        parent::__construct($model);

        $this->originalDetail = $this->model->details->toArray();
        $this->metaConfig = new JournalEntryMetaConfig();
    }

    public function addDetail($id, $coaId, $amount, $notes, $position = 0, $refNo = null)
    {
        $this->details[] = [
            'id' => $id,
            'coa_id' => $coaId,
            'amount' => $amount,
            'notes' => $notes,
            'position' => $position,
            'ref_no' => $refNo,
            'position' => $position
        ];
    }

    public function addDeletedDetail($id)
    {
        $this->deleted_details[] = $id;
    }

    public function setBusinessCodeId($businessCodeId)
    {
        $this->businessCodeId = $businessCodeId;
    }

    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;
    }
    
    public function setDepartmentId($departmentId)
    {
        $this->departmentId = $departmentId;
    }

    public function setRefNoPostfix($refNoPostfix)
    {
        $this->refNoPostfix = $refNoPostfix;
    }

    public function delete($permanent = null)
    {
        if ($this->getAccessControl())
            $this->filterByAccessControl('journal_entry_delete');

        JournalEntryDetail::where('journal_entry_id', $this->model->id)
                                ->delete();

        // $this->model->delete();
        parent::delete('permanent');
    }

    public function deleteDetail($id)
    {
        JournalEntryDetail::where('id', $id)->delete();
    }

    public function validate()
    {
        $this->validateBasic();
        $this->validateParentChild();
        $this->validateDetail();
    }

    private function validateBasic()
    {
        //cDetail = collection Detail
        //dDetail = debit Detail
        //kDetail = kredit Detail
        $cDetail = collect($this->details);
        $dDetail = $cDetail->where('amount', '>', 0);
        $kDetail = $cDetail->where('amount', '<', 0);

        //mapping amount to positive
        $mDDetail = $dDetail->map(function ($d) {
            $data = $d;
            $data['amount'] = abs($d['amount']);

            return $data;
        });

        $mKDetail = $kDetail->map(function ($k) {
            $data = $k;
            $data['amount'] = abs($k['amount']);

            return $data;
        });

        //dTotal = debit Total
        //kTotal = kredit Total
        $dTotal = $mDDetail->sum('amount');
        $kTotal = $mKDetail->sum('amount');

        $fields = [
            'total_debit' => (float) $dTotal,
            'total_kredit' => (float) $kTotal,

            'is_parent' => $this->model->is_parent,
            'journal_entry_category_id' => $this->model->journal_entry_category_id,
            'journal_entry_id' => $this->model->journal_entry_id,
        ];

        $rules = [
            'total_debit' => 'numeric|same:total_kredit',
            'is_parent' => 'numeric|in:1,0',
            'journal_entry_category_id' => [
                'nullable',
                'exists:category,id'
            ],
            'journal_entry_id' => [
                'nullable',
                'exists:journal_entry,id'
            ],
        ];

        $validator = Validator::make($fields, $rules);
        self::validOrThrow($validator);
    }

    private function validateParentChild()
    {
        // if journal entry is parent then it's musn't have parent
        $ValidParent = 0;
        $validChild = 0;

        $id = $this->model->id;
        $isParent = $this->model->is_parent;
        $journalEntryId = $this->model->journal_entry_id;
        $journalEntryCategoryId = $this->model->journal_entry_category_id;

        if ($isParent == 1) {
            if ($journalEntryId == null && $journalEntryCategoryId != null)
                $ValidParent = 1;
        } else {
            if ($journalEntryId != null && $id != $journalEntryId)
                $validChild = 1;
        }

        $fields = [
            'is_parent' => $isParent,
            'valid_parent' => $ValidParent,
            'valid_child' => $validChild,
        ];

        $rules = [
            'valid_parent' => 'exclude_if:is_parent,0|numeric|in:1',
            'valid_child' => 'exclude_if:is_parent,1|numeric|in:1',
        ];

        $messages = [
            'valid_parent.in' => 'Jenis wajib diisi jika Jenis Journal adalah Parent.',
            'valid_child.in' => 'Parent JE wajib diisi jika Jenis Journal adalah Child.',
        ];

        $validator = Validator::make($fields, $rules, $messages);
        self::validOrThrow($validator);
    }

    private function validateDetail()
    {
        $fields = [
            'details' => $this->details
        ];

        $rules = [
            'details' => 'required',

            'details.*.coa_id' => 'required|exists:category,id',
            'details.*.amount' => 'required|numeric',
            'details.*.notes' => 'nullable',
        ];

        $validator = Validator::make($fields, $rules);
        self::validOrThrow($validator);
    }

    private function generateData()
    {
        // Generate new ref no if empty
        // PR/AABBBB/MM.YY (Contoh : PR/010001/01.20)
        // if refNoPostfix empty, replace model->ref_no
        if(empty($this->refNoPostfix))
            $this->model->ref_no = $this->generateRefNo(4, $this->getPrefix(), $this->getPostfix());

        if ($this->model->is_parent == 0) {
            $this->model->journal_entry_category_id = Model::find($this->model->journal_entry_id)->journal_entry_category_id;

            $this->model->ref_no = $this->generateRefNo(4, $this->getPrefix(), $this->getPostfix());
        }
    }

    private function generateParent()
    {
        if (!isset($this->model->is_parent))
            $this->model->is_parent = 1;
    }

    public function save()
    {
        if ($this->getAccessControl())
            $this->filterByAccessControl('journal_entry_create');

        $this->generateParent();
        $this->validate();
        $this->generateData();

        // Header For Log
        $headerOrigin = $this->original;

        if($headerOrigin && !$headerOrigin['created'] instanceof \DateTime)
            $headerOrigin['created'] = new \DateTime($headerOrigin['created']);

        $headerModified = $this->model->toArray();

        // Detail original data
        // $detailOrigin = $this->model->details->toArray();
        $detailOrigin = $this->originalDetail;

        // var_dump($this->originalDetail);
        // var_dump('==========================');
        // var_dump($cekOriginal);
        // die;

        $this->model->save();

        foreach ($this->deleted_details as $x => $value) {
            $this->deleteDetail($value);
        }

        $this->saveDetails();
        $this->saveMeta();

        // Detail after save
        $detailModified = JournalEntryDetail::where('journal_entry_id', $this->model->id)
                                                ->get()
                                                ->toArray();

        if($headerOrigin) {
            // into logger
            $user = new User;
            if ($this->getAccessControl())
                $user = $this->getAccessControl()->getUser();

            $logger = new JournalEntryLogger($user);
            // Passing header origin and modified data
            $logger->setHeader($headerOrigin, $headerModified);
            // var_dump($detailOrigin, $detailModified); die;
            // Passing detail origin dan modified data
            $logger->addDetailAsArray('detail', $detailOrigin, $detailModified);
            // Get generated log message
            $notes = $logger->getOnUpdatedLog();
            // If empty, we don't need to save it
            if(!empty($notes)) {
                LogM::create([
                    'fk_id' => $this->model->id,
                    'table_name' => $this->model->getTable(),
                    'notes' => $notes,
                    'created' => new \DateTime
                ]);
            }
        }
    }

    private function saveDetails()
    {
        $cData = collect($this->details);
        $cSortedDebit = $cData->where('amount', '>', 0)->sortByDesc('amount');
        $sortedDebit = $cSortedDebit->values()->all();

        $cSortedCredit = $cData->where('amount', '<', 0)->sortBy('amount');
        $sortedCredit = $cSortedCredit->values()->all();

        $list = array_merge($sortedDebit, $sortedCredit);
        $list = collect($list);
        $details = Position::makePositionSequential($list->sortBy('position')->values()->all());

        foreach ($details as $key => $value) {
            $detail = JournalEntryDetail::findOrNew($value['id']);
            $detail->journal_entry_id = $this->model->id;
            $detail->coa_id = $value['coa_id'];
            $detail->ref_no = $value['ref_no'];
            $detail->amount = $value['amount'];
            $detail->notes = $value['notes'];
            $detail->position = $value['position'];
            $detail->save();
        }
    }

    private function saveMeta()
    {
        $list = $this->model->metaIgnoreKeys()->get();
        $fkId = $this->model->id;
        $tableName = $this->model->getTable();
        $metaManager = new MetaManager($list, $fkId, $tableName);

        // ==== Tunjangan dll ====
        $metaManager->addDetail(JournalEntryMetaConfig::DEPARTMENT_ID, $this->departmentId);
        $metaManager->addDetail(JournalEntryMetaConfig::EMPLOYEE_ID, $this->employeeId);
        $metaManager->addDetail(JournalEntryMetaConfig::BUSINESS_CODE_ID, $this->businessCodeId);

        $metaManager->saveAllAndDelete();
    }

    public function toArray()
    {
        $this->filterByAccessControl('journal_entry_read');

        $data = $this->model->toArray();

        $data['created'] = DateFormat::iso(new \DateTime($this->model->created));
        $data['ref_no_prefix'] = $this->model->category->name;
        $data['ref_no_postfix'] = str_replace($this->model->category->name . "/", "", $data['ref_no']);

        // ===== Transform Meta into $data ====
        // Get meta config
        $metaConfig = $this->metaConfig;
        // Get meta and ignore keys
        $metas = $this->model->metaIgnoreKeys($metaConfig->getIgnoreKeys())->get();

        // Transport meta into regular array
        $metaList = $metaConfig->transform($metas->toArray());
        // Merge into data
        $data = array_merge($data, $metaList);
        // ===== Transform Meta into $data(end) ====

        foreach ($data['details'] as $key => $value) {
            $coa = Category::find($value['coa_id']);

            $data['details'][$key]['coa'] = $coa->name . ' - ' . $coa->label;
        }

        $data['category'] = $data['table_name'];
        $data['source_id'] = $data['fk_id'];
        $data['source_ref_no'] = null;
        if ($data['table_name'] == 'goods_received') {
            $data['source_ref_no'] = GoodsReceived::withTrashed()->find($data['fk_id'])->ref_no;
        } elseif ($data['table_name'] == 'purchase_order') {
            $data['source_ref_no'] = PurchaseOrder::withTrashed()->find($data['fk_id'])->ref_no;
        } elseif ($data['table_name'] == 'invoice') {
            $data['source_ref_no'] = Invoice::find($data['fk_id'])->ref_no;
        }

        return $data;
    }

    /*
    // PR/AABBBB/MM.YY (Contoh : PR/010001/01.20)
    AA : Nomor Kode Departement
    BBBB : No urut (4 digit)
    MM : Bulan (2 digit angka)
    YY : tahun (2 digit - Note : dapat diubah setiap tahun)
     *
     * @return String PR/AA
     */
    private function getPrefix()
    {
        $businessCode = Category::find($this->businessCodeId);
        $personalCode = Meta::where('table_name', 'person')->where('fk_id', $this->employeeId)->where('key', 'personal_code')->first();
        $departmentCode = Category::find($this->departmentId);

        $created = $this->model->created;
        if (!$this->model->created instanceof \DateTime) {
            $created = new \DateTime($this->model->created);
        }

        if ($this->model->journal_entry_category_id == null)
            $journalEntryCategoryName = Model::find($this->model->journal_entry_id)->category->name;
        else
            $journalEntryCategoryName = $this->model->category->name;

        $prefix = sprintf('%s/%s-%s-%s/%s/%s/',
            $journalEntryCategoryName,
            ($businessCode->name ?? '00'),
            ($departmentCode->name ?? '00'),
            ($personalCode->value ?? '00'),
            $created->format('m'),
            $created->format('Y')
        );

        if ($this->model->journal_entry_id) {
            $journalEntryParent = Model::find($this->model->journal_entry_id);

            $prefix = sprintf('%s_', $journalEntryParent->ref_no);
        }

        return $prefix;
    }

    /*
    // PR/AABBBB/MM.YY (Contoh : PR/010001/01.20)
    AA : Nomor Kode Departement
    BBBB : No urut (4 digit)
    MM : Bulan (2 digit angka)
    YY : tahun (2 digit - Note : dapat diubah setiap tahun)
     *
     * @return String /MM.YY
     */
    private function getPostfix()
    {
        // return '/' . date('m.y');
    }

    public function clearDetail()
    {
        JournalEntryDetail::where('journal_entry_id', $this->model->id)->delete();
    }
}
