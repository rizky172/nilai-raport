<?php

namespace App\Libs\Repository;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

use App\Libs\Repository\AbstractRepository;
use App\Libs\Repository\Media;

use App\Libs\Helpers\ResourceUrl;

use App\Models\Config as Model;
use App\Models\Meta;
use App\Models\Person;

class Config extends AbstractRepository
{

    const SYNC_PRICE_PROCESS_KEY = 'sync_price_process_id';

    const ALLOWED_KEY = [
        // Company Information
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
        'student_permission_group_id',
        'teacher_permission_group_id'
    ];

    private $list = [];

    private $logo = [];
    private $banner = [];
    private $favIcon = [];
    private $_delete_logo = [];
    private $_delete_banner = [];
    private $_delete_fav_icon = [];

    // private $logoNavigasi = null;
    // private $_deletedLogoNavigasi = null;
    private $profitSharing = null;
    private $sponsor = null;

    public function __construct()
    {
        parent::__construct(new Model());
    }

    public function addLogo($logo)
    {
        $this->logo = $logo;
    }

    public function addBanner($banner)
    {
        $this->banner = $banner;
    }

    public function addFavIcon($favIcon)
    {
        $this->favIcon = $favIcon;
    }

    // public function addLogoNavigasi($logo)
    // {
    //     $this->logoNavigasi = $logo;
    // }

    // public function addDeleteLogoNavigasi($id)
    // {
    //     $this->_deletedLogoNavigasi = $id;
    // }

    public function deleteLogo($logo)
    {
        $model = Meta::where('table_name', 'config')
            ->where('key', 'logo')
            ->first();

        $repo = new Media($model);
        $repo->delete();
    }

    public function deleteBanner($banner)
    {
        $model = Meta::where('table_name', 'config')
            ->where('key', 'banner')
            ->first();

        $repo = new Media($model);
        $repo->delete();
    }

    public function addDeleteFavIcon($favIcon)
    {
        $model = Meta::where('table_name', 'config')
            ->where('key', 'media')
            ->first();

        $repo = new Media($model);
        $repo->delete();
    }

    public function update($key, $value)
    {
        $this->list[] = [
            'key' => $key,
            'value' => $value
        ];
    }

    public static function get($key)
    {
        $value = null;

        $row = Model::where('key', $key)->first();
        if (!empty($row))
            $value = $row->value;

        return $value;
    }

    public function validate()
    {
        $this->validateBasic();
    }

    private function validateBasic()
    {
        $collection = collect($this->list);

        // Validate value
        $fields = [
            'setting' => $this->list
        ];

        $rules = array(
            'setting.*.key' => 'required',
            'setting.*.value' => 'nullable'
        );

        $validator = Validator::make($fields, $rules);
        AbstractRepository::validOrThrow($validator);
    }

    private function generateData()
    {
        foreach ($this->list as $x) {
            $key = $x['key'];
            $value = $x['value'];
        }
    }

    public function save()
    {
        $this->filterByAccessControl('setting_create');
        $this->validate();
        // $this->generateData();

        if (isset($this->logo) && !empty($this->logo))
            $this->saveLogo();

        if (isset($this->banner) && !empty($this->banner))
            $this->saveBanner();

        if (isset($this->favIcon) && !empty($this->favIcon))
            $this->saveFavIcon();

        // if (isset($this->logoNavigasi)){
        //     $this->saveLogoNavigasi();
        // }

        foreach ($this->list as $x) {
            $key = $x['key'];
            $value = $x['value'];

            $row = Model::where('key', $key)->first();
            if (empty($row)) {
                $row = new Model();
                $row->key = $key;
            }

            $row->value = $value;
            $row->save();
        }
    }

    public function saveLogo()
    {
        foreach ($this->logo as $x => $value) {
            $row = Model::firstOrNew(['key' => 'logo', 'value' => 'media']);
            $row->save();

            $meta = Meta::firstOrNew(['key' => 'logo', 'table_name' => 'config']);
            $meta->fk_id = $row->id;

            $repo = new Media($meta);
            if (!empty($this->logo[$x]))
                $repo->addFile($this->logo[$x]);
            $repo->save();
        }
    }

    public function saveBanner()
    {
        foreach ($this->banner as $x => $value) {
            $row = Model::firstOrNew(['key' => 'banner', 'value' => 'media']);
            $row->save();

            $meta = Meta::firstOrNew(['key' => 'banner', 'table_name' => 'config']);
            $meta->fk_id = $row->id;

            $repo = new Media($meta);
            if (!empty($this->banner[$x]))
                $repo->addFile($this->banner[$x]);
            $repo->save();
        }
    }

    public function saveFavIcon()
    {
        foreach ($this->favIcon as $x => $value) {
            $row = Model::firstOrNew(['key' => 'fav_icon', 'value' => 'media']);
            $row->save();

            $meta = Meta::firstOrNew(['key' => 'media', 'table_name' => $row->getTable()]);
            $meta->fk_id = $row->id;

            $repo = new Media($meta);
            if (!empty($this->favIcon[$x]))
                $repo->addFile($this->favIcon[$x]);
            $repo->save();
        }
    }

    // public function saveLogoNavigasi()
    // {
    //     $row = Model::where('key', 'logo_navigasi')->first();
    //     if(empty($row)) {
    //         $row = new Model();
    //         $row->key = 'logo_navigasi';
    //     }

    //     $row->value = 'media';
    //     $row->save();

    //     $meta = Meta::where('key', 'logo_navigasi')->where('table_name', 'config')->first();
    //     if(empty($meta))
    //         $meta = new Meta;

    //     $meta->fk_id = $row->id;
    //     $meta->key = 'logo_navigasi';
    //     $meta->table_name = 'config';

    //     $repo = new Media($meta);
    //     $repo->addFile($this->logoNavigasi);
    //     $repo->save();
    // }

    public static function toArray()
    {
        $list = [];
        $setting = Model::all();
        $exclude = ['smtp_password'];

        $setingList = [];
        foreach ($setting as $x) {
            if (!in_array($x->key, $exclude)) {
                $setingList[] = [
                    'key' => $x->key,
                    'value' => $x->value
                ];
            }
        }

        //create formatted array for setting
        $formattedSetting = array_column($setingList, 'value', 'key');

        //just fill allowed key with value
        foreach (self::ALLOWED_KEY as $x => $value) {
            $list[$value] = null;
            //find exist key
            if (array_key_exists($value, $formattedSetting))
                $list[$value] = $formattedSetting[$value];
        }

        $media = Model::whereIn('key', ['logo', 'banner', 'logo_navigasi','fav_icon'])->get();

        $configLogo = $media->where('key', 'logo')->first();
        $configBanner = $media->where('key', 'banner')->first();
        $configFavIcon = $media->where('key', 'fav_icon')->first();
        // $configLogoNavigasi = $media->where('key', 'logo_navigasi')->first();

        if ($configLogo) {
            $logo = Meta::where([
                'fk_id' => $configLogo->id,
                'key' => 'logo',
                'table_name' => 'config'
            ])->first();

            $list['logo'] = [];

            if ($logo) {
                $list['logo']['id'] = $logo->id;
                $list['logo']['url'] = ResourceUrl::config($logo->value);
                $list['logo_name'] = $logo->value;
            }
        } else {
            $list['logo'] = [
                'url' => null
            ];
            $list['logo_name'] = null;
        }

        if ($configBanner) {
            $banner = Meta::where([
                'fk_id' => $configBanner->id,
                'key' => 'banner',
                'table_name' => 'config'
            ])->first();


            $list['banner'] = [];

            if ($banner) {
                $list['banner']['id'] = $banner->id;
                $list['banner']['url'] = ResourceUrl::config($banner->value);
            }
        } else {
            $list['banner'] = [
                'url' => null
            ];
        }

        if ($configFavIcon) {
            $favIcon = Meta::where([
                'fk_id' => $configFavIcon->id,
                'key' => 'media',
                'table_name' => $configFavIcon->getTable()
            ])->first();

            $list['fav_icon'] = [];
            $list['fav_icon_name'] = null;

            if ($favIcon) {
                $list['fav_icon']['id'] = $favIcon->id;
                $list['fav_icon']['url'] = ResourceUrl::config($favIcon->value);
                $list['fav_icon_name'] = $favIcon->value;
            }
        } else {
            $list['fav_icon'] = [
                'url' => null
            ];
            $list['fav_icon_name'] = null;
        }

        // if ($configLogoNavigasi) {
        //     $logoNavigasi = Meta::where([
        //                 'fk_id' => $configLogoNavigasi->id,
        //                 'key' => 'logo_navigasi',
        //                 'table_name' => 'config'
        //             ])->first();


        //     $list['logo_navigasi'] = [];

        //     if ($logoNavigasi) {
        //         $list['logo_navigasi']['id'] = $logoNavigasi->id;
        //         $list['logo_navigasi']['url'] = ResourceUrl::config($logoNavigasi->value);
        //     }
        // } else {
        //     $list['logo_navigasi'] = [
        //         'url' => null
        //     ];
        // }

        $list['_delete_logo'] = [];
        $list['_delete_banner'] = [];
        $list['_delete_fav_icon'] = [];
        // $list['_delete_logo_navigasi'] = [];

        return $list;
    }
}
