<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Libs\Repository\Config;

class ApiConfigController extends ApiController
{
    public function index()
    {
        $this->jsonResponse->setData(Config::toArray());
        return $this->jsonResponse->getResponse();
    }

    public function store(Request $request)
    {
        $logo = [];
        $banner = [];
        $favIcon = [];
        // $logoNavigasi = [];

        if ($request->file('files')) {
            $file = $request->file('files');

            if(!empty($file['logo']))
                $logo = $file['logo'];

            if(!empty($file['banner']))
                $banner = $file['banner'];

            if(!empty($file['fav_icon']))
                $favIcon = $file['fav_icon'];

            // if(!empty($file['logo_navigasi'])){
            //     $logoNavigasi = $file['logo_navigasi'];
            // }
        };

        $request = json_decode($request->data);

        $repo = new Config();

        if (!empty($logo))
            $repo->addLogo($logo);

        if (!empty($banner))
            $repo->addBanner($banner);

        if (!empty($favIcon))
            $repo->addFavIcon($favIcon);

        // if (!empty($logoNavigasi)){
        //     $repo->addLogoNavigasi($logoNavigasi);
        // }

        if ($request->_delete_logo)
            foreach ($request->_delete_logo as $x)
                $repo->addDeleteLogo($x);

        if ($request->_delete_banner)
            foreach ($request->_delete_banner as $x)
                $repo->addDeleteBanner($x);

        if ($request->_delete_fav_icon)
            foreach ($request->_delete_fav_icon as $x)
                $repo->addDeleteFavIcon($x);

        // if ($request->_deleted_logo_navigasi){
        //     $repo->addDeleteLogoNavigasi($request->_deleted_logo_navigasi);
        // }

        foreach(Config::ALLOWED_KEY as $x) {
            $repo->update($x, $request->$x);
        }

        $repo->save();

        $this->jsonResponse->setMessage('Configuration saved successfully.');
        return $this->jsonResponse->getResponse();
    }
}
