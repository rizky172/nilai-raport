<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Libs\Libs;

use Carbon\Carbon;

class MemberExpiredCalculator
{
    const MAX_DAY_BEFORE_UPGRADE = 14;
    private $expiredDate;

    public function __construct(\DateTime $expiredDate)
    {
        $this->expiredDate = $expiredDate;
    }

    // Check whatever expired date is reach out
    public function isExpired(\DateTime $date)
    {
        return $date > $this->expiredDate;
    }

    // Check if permitted to upgrade based on expired date
    public function isUpgradeable(\DateTime $date)
    {
        $expiredDate = Carbon::instance(clone $this->expiredDate);
        $expiredDate->subDays(self::MAX_DAY_BEFORE_UPGRADE);

        return $date >= $expiredDate;
    }

    public function getNextExpiredDate()
    {
        $date = Carbon::instance(clone $this->expiredDate);
        $date->addYears();

        return $date;
    }
}
