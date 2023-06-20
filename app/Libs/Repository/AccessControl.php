<?php
namespace App\Libs\Repository;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

use App\Models\User;
use App\Models\Person;
use DB;

use Wevelope\Wevelope\AccessControl\AbstractAccessControl;
use App\Libs\Repository\AbstractRepository;

class AccessControl extends AbstractAccessControl
{
    private $permission = [];
    
    public function __construct(User $model)
    {
        parent::__construct($model);
        $this->permission = $this->getPermissions();
    }

    public function getPermissionGroups()
    {
        $permissionGroups = User::select('permission.name')
                ->join('meta as permission_group', 'permission_group.fk_id', '=', 'user.id')
                ->join('category as permission', 'permission.id', '=', 'permission_group.value')
                ->where('permission_group.fk_id', $this->model->id)
                ->where('permission.group_by', 'permission_group')
                ->get();

        return $permissionGroups;
    }

    // @return Collection {name: 'value'}
    public function getPermissions()
    {
        $permissions = DB::table('meta as permissionGroup')
            ->join('meta as permission', 'permissionGroup.value', '=',
            'permission.fk_id')
            ->join('category', 'permission.value', '=', 'category.id')
            ->where('permission.table_name', 'category')
            ->where('permission.key', 'permission_id')
            ->where('permissionGroup.table_name', 'user')
            ->where('permissionGroup.key', 'permission_group_id')
            ->where('permissionGroup.fk_id', $this->model->id)
            ->orderBy('category.name', 'asc')
            ->select('category.name')
            ->distinct()
            ->get();

        return $permissions->sortBy('name');
    }

    public function hasAccess($name)
    {
        return !empty($this->permission->where('name', $name)->first());
    }

    public function hasAccesses($listName)
    {
        $isHasAccess = $this->permission;
        foreach($listName as $x)
            $isHasAccess->where('name', $x);

        return !empty($isHasAccess);
    }

    public function getUser()
    {
        return $this->getModel();
    }

    public function isCustomer()
    {
        if ($this->model->category->name == 'customer') {
            return true;
        }

        return false;
    }

    public function isDriver()
    {
        if ($this->model->category->name == 'customer') {
            return true;
        }

        return false;
    }

    public function hasAccessDepartment($departmentId)
    {
        if (!empty($departmentId)) {
            $user = $this->getUser();
            $userDepartmentId = $user->person
                                     ->accessControlDepartmentId
                                     ->pluck('value')
                                     ->toArray();

            if (!in_array($departmentId, $userDepartmentId)) {
                $messages = 'Anda tidak memiliki akses untuk department ini';
                self::throwUnauthorizedException($messages);
            }
        }
    }

    public function hasAccessDepartmentOfPerson($personId)
    {
        $user = $this->getUser();
        $userDepartmentId = $user->person->accessControlDepartmentId->pluck('value')
            ->toArray();

        $personData = Person::find($personId);
        $personDepartmentId = $personData->meta->where('key', 'department_id')
            ->whereIn('value', $userDepartmentId)
            ->toArray();

        if (empty($personDepartmentId))
            self::throwUnauthorizedException();
    }

    public function hasAccessPerson($personId)
    {
        $user = $this->getUser();

        if ($personId != $user->person_id)
            self::throwUnauthorizedException();
    }

    public function hasPerson()
    {
        $user = $this->getUser();

        if (empty($user->person_id)) {
            $messages = 'Anda tidak terasosiasi dengan data Member';
            self::throwUnauthorizedException($messages);
        }
    }
}