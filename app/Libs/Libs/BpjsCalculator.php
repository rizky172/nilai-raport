<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Libs\Libs;

/**
 * Description of JamsostekCalculator
 *
 * @author blackhat
 */
class BpjsCalculator
{
    private $monthlySalary;
    // Tunjangan makan dan trasnport
    private $tmt;
    // Tunjangan lain-lain
    private $tl;

    public function __construct($monthlySalary, $tunjanganMakanTransport, $tunjanganLain)
    {
        $this->monthlySalary = $monthlySalary;
        $this->tmt = $tunjanganMakanTransport;
        $this->tl = $tunjanganLain;
    }

    public function getTotal()
    {
        return $this->monthlySalary + $this->tmt + $this-tl;
    }

    // Jaminan Sosial Ketenagakerjaan
    public function getKaryawanJSK()
    {
        return 0;
    }

    // Jaminan Sosial Ketenagakerjaan
    public function getPerusahaanJSK()
    {
        return 0;
    }
}
