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
class JamsostekCalculator
{
    private $monthlySalary;
    // Tunjangan makan dan trasnport
    private $tunjanganMakanTransport;
    // Tunjangan lain-lain
    private $tunjanganLain;

    public function __construct($monthlySalary, $tunjanganMakanTransport, $tunjanganLain)
    {
        $this->monthlySalary = $monthlySalary;
        $this->tunjanganMakanTransport = $tunjanganMakanTransport;
        $this->tunjanganLain = $tunjanganLain;
    }

    public function getTotal()
    {
        return $this->monthlySalary + $this->tunjanganMakanTransport + $this->tunjanganLain;
    }

    // Jaminan hari tua
    public function getKaryawanJHT()
    {
        return $this->getTotal() * 0.02;
    }

    // Jaminan pensiun
    public function getKaryawanJP()
    {
        return $this->getTotal() * 0.01;
    }

    // Jaminan hari tua
    public function getPerusahaanJHT()
    {
        return $this->getTotal() * 0.037;
    }

    // Jaminan kecelakan kerja
    public function getPerusahaanJKK()
    {
        return 0;
    }

    // Jaminan kematian
    public function getPerusahaanJK()
    {
        return 0;
    }

    // Jaminan pensiun
    public function getPerusahaanJP()
    {
        return 0;
    }
}
