<?php
namespace App\Libs\Repository\Finder;

use App\Models\Person as Model;
class PersonFinder extends AbstractFinder
{
    private $category;
    private $includeId;
    private $excludeId;
    private $typeId;

    public function __construct()
    {
        parent::__construct();

        $this->query = Model::select(
            'person.*'
        );

        $this->query->join('category', 'category.id', '=', 'person.person_category_id');
        // $this->query->join('category as tier', 'tier.id', '=', 'person.tier_id');
        // $this->query->leftJoin('user', 'user.person_id', '=', 'person.id');
    }

    public function setOnlyTrashed($isDeleted)
    {
        if ($isDeleted == 1){
            $this->query->onlyTrashed();
        }
    }

    public function setWithTrashed($withTrashed)
    {
        if ($withTrashed == 1) {
            $this->query->withTrashed();
        }
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function setIncludeId($includeId)
    {
        $this->includeId = $includeId;
    }

    public function setExcludeId($excludeId)
    {
        $this->excludeId = $excludeId;
    }

    public function orderBy($columnName, $orderBy)
    {
        switch($columnName) {
            case 'ref_no':
                $this->query->orderBy('person.ref_no', $orderBy);
                break;
        }
    }

    private function whereKeyword()
    {
        $keyword = $this->keyword;

        if(!empty($keyword)) {
            $list = explode(' ', $keyword);
            $list = array_map('trim', $list);

            $this->query->where(function($query) use ($list) {
                foreach($list as $x) {
                    $pattern = '%' . $x . '%';
                    $query->orWhere('person.name', 'like', $pattern);
                    $query->orWhere('person.email', 'like', $pattern);
                    $query->orWhere('person.ref_no', 'like', $pattern);
                }
            });
        }
    }

    private function whereCategory()
    {
        if (isset($this->category)) {
            $this->query->where('category.name', $this->category);
        }
    }

    private function whereIncludeId()
    {
        if (isset($this->includeId)) {
            $this->query->withTrashed();
            $this->query->orWhere('person.id', $this->includeId);
        }
    }

    private function whereExcludeId()
    {
        if (isset($this->excludeId)) {
            $this->query->where('person.id', '!=', $this->excludeId);
        }
    }

    public function doQuery()
    {
        $this->whereCategory();
        $this->whereKeyword();
        $this->whereExcludeId();
        $this->whereIncludeId();
    }
}
