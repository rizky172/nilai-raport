<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Libs\Libs;

/**
 * Description of EmployeeCalculator
 *
 * @author blackhat
 */
class EmployeeCalculator
{
    private $monthlySalary;
    // Tunjangan makan dan trasnport
    private $tunjanganMakanTransport;
    // Tunjangan prestasi
    private $tunjanganPrestasi;
    // Tunjangan jabatan
    private $tunjanganJabatan;
    // Tunjangan lain-lain
    private $tunjanganLain;

    public function __construct($monthlySalary, $tunjanganMakanTransport, $tunjanganPrestasi, $tunjanganJabatan, $tunjanganLain)
    {
        $this->monthlySalary = $monthlySalary;
        $this->tunjanganMakanTransport = $tunjanganMakanTransport;
        $this->tunjanganPrestasi = $tunjanganPrestasi;
        $this->tunjanganJabatan = $tunjanganJabatan;
        $this->tunjanganLain = $tunjanganLain;
    }

    public function getTotal()
    {
        return $this->monthlySalary + $this->tunjanganMakanTransport + $this->tunjanganPrestasi + $this->tunjanganJabatan + $this->tunjanganLain;
    }

    // Jamsostek Karyawan JHT
    public function getJamsostekKaryawanJHT()
    {
        return $this->getTotal() * 0.02;
    }

    // Jamsostek Karyawan JP
    public function getJamsostekKaryawanJP()
    {
        return $this->getTotal() * 0.01;
    }

    // Jamsostek Perusahaan JHT
    public function getJamsostekPerusahaanJHT()
    {
        return $this->getTotal() * 0.037;
    }

    // Jamsostek Perusahaan JKK
    public function getJamsostekPerusahaanJKK()
    {
        return $this->getTotal() * 0.0089;
    }

    // Jamsostek Perusahaan JK
    public function getJamsostekPerusahaanJK()
    {
        return $this->getTotal() * 0.003;
    }

    // Jamsostek Perusahaan JP
    public function getJamsostekPerusahaanJP()
    {
        return $this->getTotal() * 0.02;
    }

    // BPJS Karyawan JKS
    public function getBPJSKaryawanJKS()
    {
        return $this->monthlySalary * 0.01;
    }

    public function getBPJSPerusahaanJKS()
    {
        return $this->monthlySalary * 0.04;
    }
}
