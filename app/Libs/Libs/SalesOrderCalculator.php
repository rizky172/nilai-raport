<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Libs\Libs;

use App\PurchaseOrderDetail;


class SalesOrderCalculator
{
    private $soDetailList;
    private $doDetailList;

    public function __construct($soDetailList, $doDetailList)
    {
        $this->soDetailList = $soDetailList;
        $this->doDetailList = $doDetailList;
    }
}
