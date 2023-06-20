<?php

namespace App\Libs\Repository;

use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

use App\Libs\Meta\MetaManager;
use App\Libs\Meta\TeacherMetaConfig;
use App\Libs\Repository\Logger\TeacherLogger;
use App\Libs\Repository\User;

use App\Models\Person as Model;
use App\Models\User as ModelUser;
use App\Models\Category;

class Teacher extends Person
{
    private $metaConfig;
    private $user = [];
    private $classId = [];
    private $majorId = [];
    private $lessonId = [];

    public function __construct(Model $model)
    {
        parent::__construct($model);

        $this->metaConfig = new TeacherMetaConfig();
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
        $this->classId[] = $classId;
    }

    public function addMajor($majorId)
    {
        $this->majorId[] = $majorId;
    }

    public function addLesson($lessonId)
    {
        $this->lessonId[] = $lessonId;
    }

    private function generateData()
    {
        if (empty($this->model->ref_no)) {
            $this->model->ref_no = $this->generateRefNo(4, $this->getPrefix(), $this->getPostfix());
        }

        $category = Category::where('name', 'teacher')
                            ->where('group_by', 'person')
                            ->first();

        $this->model->person_category_id = $category->id;
    }

    public function getPrefix()
    {
        return 'TH/';
    }

    public function getPostfix()
    {
        return '/' . date('m.y');
    }

    private function validateBasic()
    {
        $fields = [
            'ref_no' => $this->model->ref_no,
            'nip' => $this->model->nip
        ];

        $rules = [
            'ref_no' => 'required|unique:person,ref_no,'.$this->model->id,
            'nip' => 'required|numeric|unique:person,nip,'.$this->model->id,
        ];

        $messages = [
            'nip.required' => 'NIP wajib diisi.',
            'nip.numeric' => 'NIP harus angka.',
            'nip.unique' => 'NIP sudah ada.'
        ];

        $validator = Validator::make($fields, $rules, $messages);
        self::validOrThrow($validator);
    }

    public function validateConfig()
    {
        $fields = [
            'permission_group_id' => Config::get('teacher_permission_group_id')
        ];

        $rules = [
            'permission_group_id' => 'required'
        ];

        $messages = [
            'permission_group_id.required' => 'Pengaturan > Hak Akses Guru wajib diisi.'
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
            'class_id' => $this->classId,
            'major_id' => $this->majorId,
            'lesson_id' => $this->lessonId
        ];

        $rules = [
            'class_id' => 'required',
            'major_id' => 'required',
            'lesson_id' => 'required'
        ];

        $messages = [
            'class_id.required' => 'Kelas wajib diisi.',
            'major_id.required' => 'Jurusan wajib diisi.',
            'lesson_id.required' => 'Mata Pelajaran wajib diisi.'
        ];

        foreach ($this->classId as $key => $value) {
            $fields['class_id'] = $value;
            $rules['class_id'] = 'exists:category,id';
            $messages['class_id.exists'] = 'Kelas tidak valid.';
        }

        foreach ($this->majorId as $key => $value) {
            $fields['major_id'] = $value;
            $rules['major_id'] = 'exists:category,id';
            $messages['major_id.exists'] = 'Jurusan tidak valid.';
        }

        foreach ($this->lessonId as $key => $value) {
            $fields['lesson_id'] = $value;
            $rules['lesson_id'] = 'exists:category,id';
            $messages['lesson_id.exists'] = 'Mata Pelajaran tidak valid.';
        }

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
        $this->filterByAccessControl('teacher_create');

        // Header For Log
        $headerOrigin = $this->model->getOriginal();
        $headerModified = $this->model->toArray();

        parent::save();

        if($headerOrigin) {
            // into logger
            $user = $this->getAccessControl()->getUser();

            $logger = new TeacherLogger($user);
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

        foreach ($this->classId as $value) {
            $metaManager->addDetail(TeacherMetaConfig::CLASS_ID, $value);
        }

        foreach ($this->majorId as $value) {
            $metaManager->addDetail(TeacherMetaConfig::MAJOR_ID, $value);
        }

        foreach ($this->lessonId as $value) {
            $metaManager->addDetail(TeacherMetaConfig::LESSON_ID, $value);
        }

        $metaManager->saveAllAndDelete();
    }

    public function saveUser()
    {
        $permissionGroupId = [Config::get('teacher_permission_group_id')];

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
        $this->filterByAccessControl('teacher_delete');

        parent::delete($permanent);
    }

    public function toArray()
    {
        $this->filterByAccessControl('teacher_read');

        $data = parent::toArray();

        $class = $this->model->class->pluck('value')->toArray();
        $major = $this->model->major->pluck('value')->toArray();
        $lesson = $this->model->lesson->pluck('value')->toArray();

        $categoryId = array_merge($class, $major, $lesson);

        $category = Category::select('id', 'label', 'group_by')
                            ->whereIn('id', $categoryId)
                            ->whereIn('group_by', ['class','major','lesson'])
                            ->get();

        $classId = $category->filter(function($x) {
            return $x->group_by == 'class';
        })->toArray();

        $majorId = $category->filter(function($x) {
            return $x->group_by == 'major';
        })->toArray();

        $lessonId = $category->filter(function($x) {
            return $x->group_by == 'lesson';
        })->toArray();

        $data['class_id'] = $classId;
        $data['major_id'] = $majorId;
        $data['lesson_id'] = $lessonId;

        $user = $this->model->user;
        if ($user) {
            $data['username'] = $user->username;
            $data['password'] = '';
            $data['retype_password'] = '';
        }

        return $data;
    }
}
