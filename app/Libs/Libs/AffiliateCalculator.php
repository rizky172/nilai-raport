<?php

namespace App\Libs\Libs;

use Illuminate\Support\Facades\Validator;

use App\Libs\Repository\AbstractRepository;

class AffiliateCalculator
{
    private $from;
    private $to;
    private $sponsor;
    private $pointToIdr;

    public function setOldPackage($package)
    {
        $this->from = $package;
    }

    public function setNewPackage($package)
    {
        $this->to = $package;
    }

    public function setSponsorPackage($package)
    {
        $this->sponsor = $package;
    }

    public function setPointToIdr($pointToIdr)
    {
        $this->pointToIdr = $pointToIdr;
    }

    private function validateLevel()
    {
        $fields = [
            'from' => $this->from['level'],
            'to' =>  $this->to['level']
        ];

        $rules = [
            'to' => 'numeric|gt:from'
        ];

        $messages = [
            'to.gt' => 'Paket yang dibeli harus lebih tinggi dari paket saat ini.'
        ];

        $validator = Validator::make($fields, $rules, $messages);
        AbstractRepository::validOrThrow($validator);
    }

    public function getSponsorIdr()
    {
        $this->validateLevel();

        $from = $this->from;
        $to = $this->to;
        $sponsor = $this->sponsor;

        if ($sponsor['level'] <= $from['level'])
            return 0;

        if ($to['level'] > $sponsor['level'])
            return $sponsor['bonus_idr'];

        $result = $to['bonus_idr'] - $from['bonus_idr'];

        return $result;
    }

    public function getSponsorPoint()
    {
        $this->validateLevel();

        $from = $this->from;
        $to = $this->to;
        $sponsor = $this->sponsor;

        if ($sponsor['level'] <= $from['level'])
            return 0;

        if ($to['level'] > $sponsor['level'])
            return $sponsor['bonus_point'];

        $result = $to['bonus_point'] - $from['bonus_point'];

        return $result;
    }

    public function getCompanyIdr()
    {
        $this->validateLevel();

        $from = $this->from;
        $to = $this->to;
        $sponsor = $this->sponsor;

        $price = $to['price'] - $from['price'];

        $pointIdr = $this->getSponsorPoint() * $this->pointToIdr;
        $used = $this->getSponsorIdr() + $pointIdr;

        $result = $price - $used;

        return $result;
    }
}
