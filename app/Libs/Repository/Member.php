<?php

namespace App\Libs\Repository;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Libs\Repository\User;
use App\Libs\Helpers\ResourceUrl;
use App\Libs\Mails\VerificationMemberEmailMail;

use App\Models\Person as Model;
use App\Models\User as ModelUser;
use App\Models\Category;
use App\Models\Meta;

class Member extends Person
{
    private $file = null;
    private $user = [];
    private $permissionGroupId = null;
    private $isActive;
    private $affiliate;

    public function __construct(Model $model)
    {
        parent::__construct($model);

        $this->isActive = 1;
    }

    public function setPermissionGroupId($permissionGroupId)
    {
        $this->permissionGroupId = $permissionGroupId;
    }

    public function setUser($userName, $password, $retypePassword)
    {
        $this->user = [
            'username' => $userName,
            'password' => $password,
            'retype_password' => $retypePassword,
        ];
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    public function setAffiliate($affiliate)
    {
        $this->affiliate = $affiliate;
    }

    public function save()
    {
        if($this->getAccessControl()) {
            $this->filterByAccessControl('member_create');

            $this->validateByRole();
        }

        if(empty($this->model->is_active)){
            $this->model->is_active = $this->model->is_active == '0' ? 0 : $this->isActive;
        }

        parent::save();
    }

    public function saveUser()
    {
        $permissionGroupId = [Config::get('member_permission_group_id')];

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
            if ($this->accessControl->hasAccess('member_full_access') && !empty($this->permissionGroupId))
                $permissionGroupId = $this->permissionGroupId;
        }

        $repo->setPermissionGroupId($permissionGroupId);

        $repo->save();
    }

    public function toArray()
    {
        if($this->getAccessControl()) {
            $this->filterByAccessControl('member_read');

            $this->validateByRole();
        }

        $data = parent::toArray();

        $baseUrl = env('APP_URL');
        $data['link'] = sprintf('%s/register?ref=%s', $baseUrl, $data['code_affiliate']);

        $user = $this->model->user;
        if ($user) {
            $data['username'] = $user->username;
            $data['password'] = '';
            $data['retype_password'] = '';
        }

        $data['file'] = null;
        $data['image_url'] = null;

        $file = $this->model->media->first();

        if ($file)
            $data['image_url'] = ResourceUrl::person($file->value);

        if(!empty($this->model->user->permissionGroups)){
            $accessControl = new AccessControl($this->model->user);
            $permissionGroups = $this->model->user->permissionGroups;
            $data['permission_group_id'] = empty($permissionGroups ) ? null : $permissionGroups->pluck('value')->all();
            $data['permission'] = $accessControl->getPermissions()->pluck('name');
            $data['permission_group'] = $accessControl->getPermissionGroups()->sortBy('name')->pluck('name');
        }

        return $data;
    }

    public function deleteUser($permanent = null)
    {
        $user = ModelUser::withTrashed()
                        ->where('person_id', $this->model->id)
                        ->first();

        if($user){
            $repo = new User($user);
            $repo->delete($permanent);
        }
    }

    public function delete($permanent = null)
    {
        if($this->getAccessControl()) {
            $this->filterByAccessControl('member_delete');

            $this->validateByRole();
        }

        parent::delete($permanent);
    }

    public function beforeSave()
    {
        $this->generateData();

        parent::beforeSave();
    }

    public function afterSave()
    {
        if (!empty($this->file)){
            $this->saveMedia($this->model, $this->file);
        }

        if (!empty($this->user)){
            $this->saveUser();
        }
    }

    protected function beforeDelete($permanent = null)
    {
        if ($permanent) {
            $this->deleteMedia();
        }

        $this->deleteUser($permanent);
    }

    private function generateData()
    {
        if (empty($this->model->ref_no)) {
            $this->model->ref_no = $this->generateRefNo(4, $this->getPrefix());
        }

        if (empty($this->model->code_affiliate)){
            $this->model->code_affiliate = $this->generateCodeAffiliate();
        }

        if(!empty($this->affiliate)) {
            $upline = Model::where('code_affiliate', $this->affiliate)->first();
            if(!empty($upline))
                $this->model->upline_person_id = $upline->id;
        }

        $this->model->person_category_id = Category::where('group_by', 'person')
                                                        ->where('name', 'member')
                                                        ->first()
                                                        ->id;

        if (empty($this->model->is_verified)) {
            $this->model->is_verified = 0;
        }

        if (empty($this->model->created)) {
            $this->model->created = new \DateTime();
        }
    }

    public function getPrefix()
    {
        return 'M';
    }

    public static function generateCodeAffiliate()
    {
        do {
            $key = "";
            for ($x = 1; $x <= 6; $x++) {
                $key .= random_int(0, 9);
            }
        } while (Model::where('code_affiliate', $key)->first());

        return $key;
    }

    private function validateBasic()
    {
        $fields = [
            'ref_no' => $this->model->ref_no,
            'email' => $this->model->email,
            'phone' => $this->model->phone,
            'upline_person_id' => $this->model->upline_person_id
        ];

        $rules = [
            'ref_no' => 'required|unique:person,ref_no,'.$this->model->id,
            'email' => 'required|email|unique:person,email,'.$this->model->id,
            'phone' => 'required',
            'upline_person_id' => [
                'nullable',
                Rule::notIn($this->model->id)
            ],
        ];

        $validator = Validator::make($fields, $rules);
        self::validOrThrow($validator);
    }

    private function validateUser()
    {
        $id = $this->model->user ? $this->model->user->id : '';
        $password = $this->model->user ? 'nullable' : 'required';

        $fields = [
            'permission_group_id' => Config::get('member_permission_group_id'),
            'username' => $this->user['username'],
            'password' => $this->user['password'],
            'retype_password' => $this->user['retype_password'],
        ];

        $rules = [
            'permission_group_id' => 'required',
            'username' => 'required|max:16|unique:user,username,' . $id,
            'password' => $password.'|min:5',
            'retype_password' => 'same:password'
        ];

        $messages = [
            'permission_group_id.required' => 'Member access rights have not been set in the settings menu',
        ];

        $validator = Validator::make($fields, $rules, $messages);
        AbstractRepository::validOrThrow($validator);
    }

    public function validate()
    {
        parent::validate();

        $this->validateBasic();
        if (!empty($this->user)){
            $this->validateUser();
        }
    }

    public function addFile(UploadedFile $file)
    {
        $this->file = $file;
    }

    private function saveMedia($card, $file)
    {
        $row = Meta::firstOrNew([
            'fk_id' => $card->id,
            'key' => 'media',
            'table_name' => $card->getTable()
        ]);

        $repo = new Media($row);

        if (!empty($row->id))
            $repo->deleteLastFile();

        $repo->addFile($file);
        $repo->save();
    }

    private function deleteMedia()
    {
        $model = Meta::where('fk_id', $this->model->id)
                        ->where('table_name', $this->model->getTable())
                        ->where('key', 'media')
                        ->first();

        if (!empty($model)) {
            $repo = new Media($model);
            $repo->delete();
        }
    }

    private function validateByRole()
    {
        if($this->getAccessControl()) {
            if (!$this->accessControl->hasAccess('member_full_access')) {
                $this->accessControl->hasPerson();

                $id = $this->model->id;
                $this->accessControl->hasAccessPerson($id);
            }
        }
    }

    public function restore()
    {
        $this->model->restore();

        $this->restoreUser();
    }

    public function restoreUser()
    {
        $user = ModelUser::withTrashed()
                        ->where('person_id', $this->model->id)
                        ->first();

        if($user){
            $repo = new User($user);
            $repo->restore();
        }
    }

    public function sendEmail()
    {
        $person = $this->model;

        $fields = [
            'email' => $person->email,
            'is_active' => $person->is_active
        ];

        $rules = [
            'email' => 'required|email',
            'is_active' => 'numeric|in:1'
        ];

        $messages = [
            'email.required' => 'Member tidak memiliki email.',
            'is_active.in' => sprintf('Member %s sudah tidak aktif.', $person->name)
        ];

        $validator = Validator::make($fields, $rules, $messages);
        self::validOrThrow($validator);

        $resetToken = Str::random(64);

        $mail = new VerificationMemberEmailMail($person);
        $mail->setResetToken($resetToken);
        $mail->sendMail();

        $person->reset_token_verified = $resetToken;
        $person->reset_token_verified_expired = Carbon::now()->addHours(3);
        $person->save();
    }
}
