<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Libs\Libs;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

use App\Libs\Repository\AbstractRepository;

use App\Category;
use App\Person;
use App\Meta;

/**
 * Description of JamsostekCalculator
 *
 * @author blackhat
 */
class Pph21Calculator
{
    const MAX_MASA_KERJA = 12; // Bulan
    const MAX_BIAYA_JABATAN = 500000; // Bulanan

    const PTKP = [
        'TK/0' => 54000000,
        'TK/1' => 58500000,
        'TK/2' => 63000000,
        'TK/3' => 67500000,

        'K/0' => 58500000,
        'K/1' => 63000000,
        'K/2' => 67500000,
        'K/3' => 72000000,

        'K/I/0' => 112500000,
        'K/I/1' => 117000000,
        'K/I/2' => 121500000,
        'K/I/3' => 126000000
    ];

    const TAX_RANGE = [
        // min, max, tax
        [0, 50000000, 0.05],
        [50000000, 250000000, 0.15],
        [250000000, 500000000, 0.25],
        [500000000, -1, 0.30]
    ];

    private $ptkpCategory;
    private $salaryPerMonth;
    private $additionalIncome = [];
    private $expenses = [];
    private $hasNpwp;
    private $masaKerja;
    private $bonus;
    private $ptkp;

    /*
     * @params int Pendapatan kotor per tahun
     * @params boolean Punya NPWP atau tidak
     */
    public function __construct($salaryPerMonth = 0, $ptkpCategory = 'TK/0', $hasNpwp = true, $masaKerja = 12)
    {
        $this->salaryPerMonth = $salaryPerMonth;
        $this->hasNpwp = $hasNpwp;
        $this->ptkpCategory = $ptkpCategory;
        $this->bonus = 0;

        $this->setMasaKerja($masaKerja);
        $this->setListPTKP(self::PTKP);
        // $this->initPTKP();
    }

    public function setListPTKP($list)
    {
        $this->ptkp = $list;
    }

    public function getMasaKerja()
    {
        return $this->masaKerja;
    }

    public function getBonus()
    {
        return $this->bonus;
    }

    public static function getListPTKPFromDb()
    {
        $maritalStatus = Category::withTrashed()->select('category.name', 'meta.value')
                                        ->where('group_by', 'marital_status')
                                        ->where('table_name', 'category')
                                        ->where('key', 'amount')
                                        ->join('meta', 'meta.fk_id', 'category.id')
                                        ->get();

        $ptkp = [];
        foreach ($maritalStatus as $key => $value) {
            $ptkp[$value['name']] = (int) $value['value'];
        }

        return $ptkp;
    }

    // Setahun
    public function setBonus($bonus)
    {
        $this->bonus = $bonus;
    }

    public function setMasaKerja($masaKerja)
    {
        $masaKerja = min(self::MAX_MASA_KERJA, $masaKerja);
        $masaKerja = max(0, $masaKerja);
        $this->masaKerja = $masaKerja;
    }

    public function setNpwp($hasNpwp)
    {
        $this->hasNpwp = $hasNpwp;
    }

    // Additional Income Per Year
    public function addAdditionalIncome($income)
    {
        $this->additionalIncome[] = $income;
    }

    // Expenses per year
    public function addExpenses($income)
    {
        $this->expenses[] = $income;
    }

    public function getExpenses()
    {
        return $this->expenses;
    }

    // Per Year
    public function getTotalIncome()
    {
        return $this->salaryPerMonth * $this->getMasaKerja() + array_sum($this->additionalIncome);
    }

    public function getSalaryPerMonth()
    {
        return $this->salaryPerMonth;
    }

    // Per bulan
    public function getBiayaJabatan()
    {
        $biayaJabatan = 0;

        // Prevent division by zero
        if ($this->getMasaKerja() > 0) {
            $biayaJabatan = $this->getTotalIncome() / $this->getMasaKerja() * 0.05;
            $biayaJabatan = min($biayaJabatan, self::MAX_BIAYA_JABATAN);
        }

        return $biayaJabatan;
    }

    public function getPtkp()
    {
        $temp = 0;
        if(isset($this->ptkp[$this->ptkpCategory]))
            $temp = $this->ptkp[$this->ptkpCategory];

        return $temp;
    }

    public function getNetto()
    {
        // var_dump($this->getTotalIncome(), $this->getBiayaJabatan(), $this->getMasaKerja(), $this->expenses); die;
        return $this->getTotalIncome() - $this->getBiayaJabatan() * $this->getMasaKerja() - array_sum($this->expenses);
    }

    public function getPkp()
    {
        $nettoInYear = $this->getNetto();
        $ptkp = $this->getPtkp();

        $pkp = max(0, $nettoInYear - $ptkp);
        $pkp = floor($pkp / 1000) * 1000;

        return $pkp;
        // return round(max(0, $nettoInYear - $ptkp), -2);
    }

    // PPH 21 yang harus dibayarkan
    public function getPph21()
    {
        $salary = $this->getPkp();
        $temp = self::calculatePph21($salary);
        if(!$this->hasNpwp)
            $temp = $temp * 1.2;

        return $temp;
    }

    public function getPph21Thr()
    {
        $pph21Thr = 0;
        $pph21 = $this->getPph21();

        $calc = clone $this;
        $calc->addAdditionalIncome($calc->getSalaryPerMonth());

        // PPH 21 THR full yang harus dibayar
        // Jadi angka ini NET yang harus karyawan bayarkan
        $pph21WithThr = $calc->getPph21();

        // var_dump($pph21, $pph21WithThr); die;

        // PPH 21 THR saja. Jadi tinggal dikurangkan dengan PPH 21 biasa dan PPH 21 FULL dengan THR
        // REF: https://www.gadjian.com/blog/2018/07/25/cara-mudah-melakukan-perhitungan-pph-21-thr-dan-bonus/
        $pph21Thr = $pph21WithThr - $pph21;

        return $pph21Thr;
    }

    public function getPph21Bonus()
    {
        $pph21Bonus = 0;
        $pph21 = $this->getPph21();

        $calc = clone $this;
        $calc->addAdditionalIncome($calc->getBonus());

        // PPH 21 THR full yang harus dibayar
        // Jadi angka ini NET yang harus karyawan bayarkan
        $pph21WithBonus = $calc->getPph21();

        // PPH 21 THR saja. Jadi tinggal dikurangkan dengan PPH 21 biasa dan PPH 21 FULL dengan THR
        // REF: https://www.gadjian.com/blog/2018/07/25/cara-mudah-melakukan-perhitungan-pph-21-thr-dan-bonus/
        $pph21Bonus = $pph21WithBonus - $pph21;

        return $pph21Bonus;
    }

    /**
     * Calculate PPH 21 based yearly salary(PKP)
     *
     * @param float Salary per year
     */
    public static function calculatePph21($salary)
    {
        if ($salary <= 0)
            return 0;

        $taxRange = self::TAX_RANGE;
        $lastTax = null;
        $tax = null;
        foreach ($taxRange as $key => $x) {
            $min = $x[0];
            $max = $x[1];
            $perc = $x[2];

            // If salary less than max then get the `tax` data
            // If until last index still didn't found, it's mean
            // The salary is more than last index
            if ($salary <= $max || $max == -1) {
                // Get tax data
                $tax = $x;

                // Get last tax data
                if (array_key_exists($key - 1, $taxRange))
                    $lastTax = $taxRange[$key - 1];

                // Don't forget to break the loop
                break;
            }
        }

        // Find last tax if $lastTax exists
        $countLastTax = 0;
        if (!empty($lastTax)) {
            $countLastTax = self::calculatePph21($lastTax[1]);
        }

        // Find this tax with $tax data
        $countTax = 0;
        if (!empty($tax))
            $countTax = ($salary - $tax[0]) * $tax[2];

        // Final Tax is This tax - last tax
        return $countTax + $countLastTax;
    }

    private static function validatePerson($personId)
    {
        $fields = Person::find($personId)->toArray();

        $rules = [
            'marital_status_id' => 'required|exists:category,id'
        ];

        $validator = Validator::make($fields, $rules);
        AbstractRepository::validOrThrow($validator);
    }

    private static function validateCreateByInvoiceCategory($personId, \DateTime $date, $detail)
    {
        // Validation
        $fields = [
            'person_id' => $personId,
            'date' => $date,
            'details' => $detail,
        ];

        $rules = [
            'person_id' => 'required|exists:person,id',
            'date' => 'required|date',
            'details' => 'required',

            'details.*.invoice_detail_category_id' => 'required|exists:category,id',
            'details.*.qty' => 'required|numeric|min:0',
            'details.*.price' => 'required|numeric',
        ];

        $validator = Validator::make($fields, $rules);
        AbstractRepository::validOrThrow($validator);
    }

    /*
     * Create PPH 21 Calculator based on Invoice Detail Category
     *
     * @param array [
     *  [
     *      'invoice_detail_category_id' => 1,
     *      'qty' => 1,
     *      'price' => 50000
     *  ],
     *  .
     *  .
     *  .
     * ]
     *
     * @return Pph21Calculator
     */
    public static function createByInvoiceCategory($personId, \DateTime $date, $detail)
    {
        self::validateCreateByInvoiceCategory($personId, $date, $detail);
        self::validatePerson($personId);

        $person = Person::find($personId);
        $detail = collect($detail);
        $categories = Category::whereIn('group_by', ['salary_slip_detail', 'marital_status'])->get();

        // Mapping $detail into indexed array
        $keys = 'gaji-pokok,tunjangan-jabatan,tunjangan-makan-dan-transportasi,tunjangan-prestasi,tunjangan-lain-lain,absensi,bonus,upah-lembur,inc-sales,jamsostek-karyawan-jht,jamsostek-karyawan-jp,jamsostek-perusahaan-jkk,jamsostek-perusahaan-jk,bpjs-perusahaan-jsk,tunjangan-hari-raya,potongan-lain-lain';

        $rows = [];
        foreach(explode(',', $keys) as $x) {
            $categoryId = $categories->firstWhere('name', $x)->id;
            $temp = $detail->firstWhere('invoice_detail_category_id', $categoryId);
            $rows[$x] = !empty($temp) ? ($temp['qty'] * $temp['price']) : 0;
        }

        // Find how long has been work
        $registered = new Carbon($person->registered);
        $masaKerja = $registered->diffInMonths($date);
        $hasNpwp = (boolean) $person->is_ppn;

        $gajiPokok = $rows['gaji-pokok'];
        // var_dump('gaji', $gajiPokok);
        $keys = 'tunjangan-jabatan,tunjangan-makan-dan-transportasi,tunjangan-prestasi,tunjangan-lain-lain';
        foreach(explode(',', $keys) as $x) {
            // var_dump($x, $rows[$x]);
            // Semua tunjangan dimasukkan sebagai gaji pokok, karena tiap tahun saat THR
            // tidak hanya gaji pokok yang dibayarkan, termasuk tunjangan
            $gajiPokok += $rows[$x];
        }

        $keys = 'absensi';
        foreach(explode(',', $keys) as $x) {
            // var_dump($x, $rows[$x]);
            // Semua tunjangan dimasukkan sebagai gaji pokok, karena tiap tahun saat THR
            // tidak hanya gaji pokok yang dibayarkan, termasuk tunjangan
            $gajiPokok -= abs($rows[$x]);
        }

        // var_dump($gajiPokok);
        $maritalStatus = $categories->firstWhere('id', $person->marital_status_id);
// var_dump($person->martial_status_id); die;
        $calc = new self($gajiPokok, $maritalStatus->name);
        $calc->setListPTKP(self::getListPTKPFromDb());
        $calc->setMasaKerja($masaKerja);
        $calc->setNpwp($hasNpwp);
        // $calc->setBonus($rows['inc-sales']);

        // Additional income
        $keys = 'bonus,upah-lembur,jamsostek-perusahaan-jkk,jamsostek-perusahaan-jk,bpjs-perusahaan-jsk,inc-sales';
        foreach(explode(',', $keys) as $x) {
            // var_dump($x, $rows[$x]);
            // Tunjangan makan 105,000
            $calc->addAdditionalIncome(abs($rows[$x] * 12));
        }

        // Expense
        $keys = 'jamsostek-karyawan-jht,jamsostek-karyawan-jp,potongan-lain-lain';
        foreach(explode(',', $keys) as $x) {
            // var_dump($x, $rows[$x]);
            $calc->addExpenses(abs($rows[$x] * 12));
        }

        // var_dump($calc);
        // var_dump($calc->getPph21());
        // die;

        return $calc;
    }
}
