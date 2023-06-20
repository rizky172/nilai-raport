<?php

namespace App\Libs\Repository;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

use App\Libs\Meta\MetaManager;
use App\Libs\Meta\StudentMetaConfig;
use App\Libs\Repository\Logger\StudentLogger;
use App\Libs\Repository\User;

use App\Models\Person as Model;
use App\Models\User as ModelUser;
use App\Models\Category;
use  App\Models\LogM;

class Student extends Person
{
    private $metaConfig;
    private $user = [];
    private $classId;

    public function __construct(Model $model)
    {
        parent::__construct($model);

        $this->metaConfig = new StudentMetaConfig();
    }

    public function setUser($userName, $password, $retypePassword)
    {
        $this->user = [
            'username' => $userName,
            'password' => $password,
            'retype_password' => $retypePassword,
        ];
    }

    public function addClass($classId)
    {
        $this->classId = $classId;
    }

    private function generateData()
    {
        if (empty($this->model->ref_no)) {
            $this->model->ref_no = $this->generateRefNo(4, $this->getPrefix(), $this->getPostfix());
        }

        $category = Category::where('name', 'student')
                            ->where('group_by', 'person')
                            ->first();

        $this->model->person_category_id = $category->id;
    }

    public function getPrefix()
    {
        return 'ST/';
    }

    public function getPostfix()
    {
        return '/' . date('m.y');
    }

    private function validateBasic()
    {
        $fields = [
            'major_id' => $this->model->major_id,
            'ref_no' => $this->model->ref_no,
            'nis' => $this->model->nis
        ];

        $rules = [
            'major_id' => 'required|exists:category,id',
            'ref_no' => 'required|unique:person,ref_no,'.$this->model->id,
            'nis' => 'required|numeric|unique:person,nis,'.$this->model->id,
        ];

        $messages = [
            'major_id.required' => 'Jurusan wajib diisi.',
            'major_id.exists' => 'Jurusan tidak valid.',
            'nis.required' => 'NIS wajib diisi.',
            'nis.numeric' => 'NIS harus angka.',
            'nis.unique' => 'NIS sudah ada.'
        ];

        $validator = Validator::make($fields, $rules, $messages);
        self::validOrThrow($validator);
    }

    public function validateConfig()
    {
        $fields = [
            'permission_group_id' => Config::get('student_permission_group_id')
        ];

        $rules = [
            'permission_group_id' => 'required'
        ];

        $messages = [
            'permission_group_id.required' => 'Pengaturan > Hak Akses Siswa wajib diisi.'
        ];

        $validator = Validator::make($fields, $rules, $messages);
        self::validOrThrow($validator);
    }

    public function validateUser()
    {
        $id = $this->model->user ? $this->model->user->id : '';

        $fields = [
            'username' => $this->user['username'],
            'password' => $this->user['password'],
            'confirm_password' => $this->user['retype_password']
        ];

        $rules = [
            'username' => 'required|max:16|unique:user,username,' . $id,
            'password' => 'nullable|min:5',
            'confirm_password' => 'same:password'
        ];

        $validator = Validator::make($fields, $rules);
        self::validOrThrow($validator);
    }

    public function validateMeta()
    {
        $fields = [
            'class_id' => $this->classId
        ];

        $rules = [
            'class_id' => 'required|exists:category,id',
        ];

        $messages = [
            'class_id.required' => 'Kelas wajib diisi.',
            'class_id.exists' => 'Kelas tidak valid.'
        ];

        $validator = Validator::make($fields, $rules, $messages);
        self::validOrThrow($validator);
    }

    public function validate()
    {
        parent::validate();

        $this->validateBasic();
        $this->validateConfig();
        $this->validateUser();
        $this->validateMeta();
    }

    public function beforeSave()
    {
        $this->generateData();

        parent::beforeSave();
    }

    public function save()
    {
        $this->filterByAccessControl('student_create');

        // Header For Log
        $headerOrigin = $this->model->getOriginal();
        $headerModified = $this->model->toArray();

        parent::save();

        if($headerOrigin) {
            // into logger
            $user = $this->getAccessControl()->getUser();

            $logger = new StudentLogger($user);
            // Passing header origin and modified data
            $logger->setHeader($headerOrigin, $headerModified);
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

    public function afterSave()
    {
        $this->saveMeta();

        if (!empty($this->user)){
            $this->saveUser();
        }
    }

    private function saveMeta()
    {
        $list = $this->model->meta;
        $fkId = $this->model->id;
        $tableName = $this->model->getTable();

        $metaManager = new MetaManager($list, $fkId, $tableName);
        $metaManager->addDetail(StudentMetaConfig::CLASS_ID, $this->classId);

        $metaManager->saveAllAndDelete();
    }

    public function saveUser()
    {
        $permissionGroupId = [Config::get('student_permission_group_id')];

        $userId = $this->model->user ? $this->model->user->id : '';
        $row = ModelUser::findOrNew($userId);

        $row->person_id = $this->model->id;
        $row->name = $this->model->name;
        $row->username = $this->user['username'];
        $row->email = $this->model->email;

        $repo = new User($row);

        if(!empty($this->user['password']) && !empty($this->user['retype_password'])) {
            $repo->setPassword($this->user['password'], $this->user['retype_password']);
        }

        if($this->getAccessControl()){
            if ($this->accessControl->hasAccess('student_full_access') && !empty($this->permissionGroupId))
                $permissionGroupId = $this->permissionGroupId;
        }

        $repo->setPermissionGroupId($permissionGroupId);

        $repo->save();
    }

    public function delete($permanent = null)
    {
        $this->filterByAccessControl('student_delete');

        parent::delete($permanent);
    }

    public function toArray()
    {
        $this->filterByAccessControl('student_read');

        $data = parent::toArray();

        $class = $this->model->class->first()->value;
        $data['class_id'] = $class;

        $user = $this->model->user;
        if ($user) {
            $data['username'] = $user->username;
            $data['password'] = '';
            $data['retype_password'] = '';
        }

        return $data;
    }

    // protected function beforeDelete($permanent = null)
    // {
    //     if ($permanent) {
    //         $fields = [
    //             'quotation' => $this->model->id,
    //             'item_customer' => $this->model->id,
    //             'user' => $this->model->id,
    //             'sales_order' => $this->model->id,
    //             'delivery_order' => $this->model->id,
    //             'invoice' => $this->model->id
    //         ];

    //         $rules = [
    //             'quotation' => 'required|unique:quotation,customer_id',
    //             'item_customer' => 'required|unique:item_customer,person_id',
    //             'user' => 'required|unique:user,person_id',
    //             'sales_order' => 'required|unique:sales_order,person_id',
    //             'delivery_order' => 'required|unique:delivery_order,person_id',
    //             'invoice' => 'required|unique:invoice,person_id'
    //         ];

    //         $messages = [
    //             'quotation.unique' => 'Customer sedang digunakan di Quotation',
    //             'item_customer.unique' => 'Customer sedang digunakan di Barang Customer',
    //             'user.unique' => 'Customer sedang digunakan di Admin',
    //             'sales_order.unique' => 'Customer sedang digunakan di Sales Order',
    //             'delivery_order.unique' => 'Customer sedang digunakan di Surat Jalan',
    //             'invoice.unique' => 'Customer sedang digunakan di Invoice'
    //         ];

    //         $validator = Validator::make($fields, $rules, $messages);
    //         self::validOrThrow($validator);
    //     }
    // }
}
