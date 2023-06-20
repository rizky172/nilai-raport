<?php

namespace App\Libs\Chart;

use App\Libs\Repository\Finder\GeneralLedgerFinder;
use App\Libs\Repository\Finder\InventoryToSalesFinder;

use App\Category;

class DashboardChart
{
    CONST COA = [
        '1101000' => 'PIUTANG USAHA',
        '4000001' => 'PENJUALAN',
        '2101000' => 'HUTANG USAHA',
        '5000000' => 'HARGA POKOK PENJUALAN'
    ];

    private $year;
    private $coaCategory;
    private $coaName;

    public function __construct($year)
    {
        $this->year = $year;
        $this->coaCategory = Category::where('group_by', 'coa')->get();
    }

    public function setIsPiutangPenjualan()
    {
        $this->coaName = array_search('PIUTANG USAHA', self::COA);
    }

    public function setIsPenjualan()
    {
        $this->coaName = array_search('PENJUALAN', self::COA);
    }

    public function setIsHutangPembelian()
    {
        $this->coaName = array_search('HUTANG USAHA', self::COA);
    }

    public function setIsPembelian()
    {
        $this->coaName = array_search('HARGA POKOK PENJUALAN', self::COA);
    }

    public function getData()
    {
        $data['labels'] = $this->getLabelsOfCharts();
        $data['datasets'] = $this->getDatasetsOfCharts();

        return $data;
    }

    private function getLabelsOfCharts()
    {
        $labels = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = sprintf('%s-%s',
                                \DateTime::createFromFormat('!m', $i)->format('m'),
                                \DateTime::createFromFormat('Y', $this->year)->format('y')
                            );
        }

        return $labels;
    }

    private function getDatasetsOfCharts()
    {
        $labels = $this->getLabelsOfCharts();
        $coaId = $this->coaCategory->firstWhere('name', $this->coaName)->id;

        $finder = new GeneralLedgerFinder();
        $finder->setCoa($coaId);
        $finder->setPage('all');

        $paginator = $finder->get();

        $total = [];
        foreach ($labels as $key => $monthOfYear) {
            $total[$key] = 0;
            foreach ($paginator as $x) {
                $date = new \DateTime($x->created);
                if ($date->format("m-y") == $monthOfYear) {
                    $total[$key] += abs($x->amount);
                }
            }
        }

        $data[] = [
            'label' => 'Total',
            'borderColor' => 'rgba(54, 162, 235, 1)',
            'tension' => 0.1,
            'fill' => false,
            'data' => $total
        ];

        return $data;
    }
}

?>