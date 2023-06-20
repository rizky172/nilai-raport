<?php
namespace App\Libs\Accounting;

use DB;

use App\Libs\Libs\TreeManipulator;

use App\Libs\Reports\GeneralLedgerTotal;
use App\Libs\Repository\Finder\GeneralLedgerFinder;
use App\WevelopeLibs\Helper\DateFormat;

use App\Category;
use App\Config;

/*
 * Get journal entry and group by coa_id, and sum each amount
 */
class JournalEntry
{
    const MAX_LEVEL = 3;
    const BALANCE_SHEET = 'balance-sheet';
    const INCOME_STATEMENT = 'income-statement';

    private $report;

    private $dateFrom;
    private $dateTo;

    /*
     * @param int Report mode, if you chosee BALANCE_SHEET it would only
     *  load balance sheet coa, and ignore all other report
     * @param string $dateFrom
     * @param string $dateTo
     */
    public function __construct($intMode = 'balance-sheet', $dateFrom, $dateTo)
    {
        $this->report = $intMode;

        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    /*
     * Get all coa but ignore cons COA
     *
     * @return Collection
     */
    public function getCoaList()
    {
        // Get ROOT COA
        $rootCoa = Category::withTrashed()
                            ->where('name', $this->report)
                            ->where('group_by', 'coa')
                            ->first();

        // var_dump($rootCoa);
        // die;
        // var_dump($rootCoa, $this->report); die;
        $ignoreCoa = Category::withTrashed()
                                ->where('category_id', $rootCoa->id)
                                ->get();

        $query = DB::table('category')
            ->select(
                'category.id as coa_id',
                'category.category_id as coa_id_parent',
                'category.name',
                'category.label',
                'report.value as report',
                'header.value as is_header',
                'group_by.value as group_by'
            )
            // Accounting is_header(COA)
            ->join('meta as header', function($query) {
                $query->on('header.table_name', '=', DB::raw("'category'"));
                $query->on('header.key', '=', DB::raw("'is_header'"));
                $query->on('header.fk_id', '=', "category.id");
            })
            // Accounting REPORT(COA)
            ->join('meta as report', function($query) {
                $query->on('report.table_name', '=', DB::raw("'category'"));
                $query->on('report.key', '=', DB::raw("'report'"));
                $query->on('report.fk_id', '=', "category.id");
            })
            // Accounting GROUP(COA)
            ->join('meta as group_by', function($query) {
                $query->on('group_by.table_name', '=', DB::raw("'category'"));
                $query->on('group_by.key', '=', DB::raw("'group'"));
                $query->on('group_by.fk_id', '=', "category.id");
            })
            ->join('category as parent', 'parent.name', 'report.value')
            ->join('category as accounting', 'accounting.id', 'parent.category_id')
            ->where('accounting.name', $this->report)
            ->orderBy('category.label', 'desc');
            // ->whereNotIn('category.id', $ignoreCoa->pluck('id')->toArray());
        
        $rows = $query->get();
        // var_dump($rows, $query->toSql()); die;
        return $rows->map(function($x) use ($ignoreCoa) {
            $x = (array) $x;

            $x['is_header'] = (int) $x['is_header'];
            if(in_array($x['coa_id_parent'], $ignoreCoa->pluck('id')->toArray())) {
                $x['coa_id_parent'] = null;
            }

            return $x;
        });
    }

    /*
     * Get journal group by COA_ID and SUM(amount)
     *
     * @return Collection
     */
    public function getJournal()
    {
        // Prepare all JOURNAL
        $query = DB::table('journal_entry')
            ->select(
                'journal_entry_detail.coa_id',
                DB::raw('SUM(journal_entry_detail.amount) as amount')
            )
            // Join with journal entry detail
            ->rightjoin('journal_entry_detail', 'journal_entry_detail.journal_entry_id', 'journal_entry.id')
            // Accounting REPORT(COA)
            ->join('meta as report', function($query) {
                $query->on('report.table_name', '=', DB::raw("'category'"));
                $query->on('report.key', '=', DB::raw("'report'"));
                $query->on('report.fk_id', '=', "journal_entry_detail.coa_id");
            })
            // All journal that related to $report
            ->join('category as parent', 'parent.name', 'report.value')
            ->join('category as accounting', 'accounting.id', 'parent.category_id')
            ->where('accounting.name', $this->report)
            ->groupBy('journal_entry_detail.coa_id', 'accounting.label');

        /*
        if(!empty($month)) {
            $query->whereMonth('journal_entry.created', $month);
        }
        if (!empty($year)) {
            $query->whereYear('journal_entry.created', $year);
        }
         */

        $query->whereDate('journal_entry.created', '>=', $this->dateFrom);
        $query->whereDate('journal_entry.created', '<=', $this->dateTo);

        // var_dump($query->toSql()); die;
        $rows = $query->get();
        $rows = $rows->map(function($x) {
            return (array) $x;
        });

        // var_dump($rows); die;

        return $rows;
    }

    /*
     * Get journal entry merge with coa list. So it would contain all coa with zero
     *  amount if doesn't have any journal entries.
     * This method also add 'level' and 'total'. 'total' is count all their childs and
     *  its own amount. It would help to generate report
     *
     * @return array
     */
    public function get()
    {
        // Prepare COA
        $coaList = collect($this->getCoaList());
        // $journal = collect($this->getJournal());
        $config = Config::whereIn('key', ['coa_id_tahun_mulai', 'coa_id_laba_rugi_tahun_berjalan', 'coa_id_laba_rugi_sd_tahun_lalu'])
                        ->get();

        // $year = $this->dateFrom->format('Y');
        $yearConfig = $config->firstWhere('key', 'coa_id_tahun_mulai')->value ?? null;
        $coaIdLabaRugiSdTahunLalu = $config->firstWhere('key', 'coa_id_laba_rugi_sd_tahun_lalu')->value ?? null;
        $coaIdLabaRugiTahunBerjalan = $config->firstWhere('key', 'coa_id_laba_rugi_tahun_berjalan')->value ?? null;

        $coaList = $coaList->map(function($x) use ($yearConfig, $coaIdLabaRugiSdTahunLalu, $coaIdLabaRugiTahunBerjalan) {
            $finder = new GeneralLedgerFinder();

            if ($this->report == self::BALANCE_SHEET || ($this->report == self::INCOME_STATEMENT && !empty($this->dateFrom))) {
                $finder->setDateStart(new \DateTime($this->dateFrom));
            }
            
            $finder->setDateFinish(new \DateTime($this->dateTo));
            $finder->setCoa($x['coa_id']);
            
            $startBalance = GeneralLedgerTotal::createFromGeneralLedgerFinder($finder);
            $startBalance = $startBalance->getTotal();

            $x['amount'] = $startBalance['total'];
            
            if ($this->report == self::BALANCE_SHEET && $x['coa_id'] == $coaIdLabaRugiSdTahunLalu) {
                // $x['amount'] = $startBalance['balance'];

                // if (!empty($yearConfig) && $year <= $yearConfig) {
                    $initialAmountLabaRugiSdTahunLalu = GeneralLedgerTotal::getTotalInitialAmount($coaIdLabaRugiSdTahunLalu);
                    $initialAmountLabaRugiTahunBerjalan = GeneralLedgerTotal::getTotalInitialAmount($coaIdLabaRugiTahunBerjalan);

                    $yearDateTo = date("Y", strtotime($this->dateTo));
                    $netProfitAfterTax = 0;
                    if ($yearDateTo >= $yearConfig) {
                        $dateFrom = '';
                        $dateTo = date('Y-m-d', strtotime('-1 day', strtotime($this->dateFrom)));

                        $incomeStatement = IncomeStatement::createFromDateRange($dateFrom, $dateTo);
                        $incomeStatement = $incomeStatement->get();
                        $netProfitAfterTax = $incomeStatement['net_profit_after_tax'];
                    }

                    $x['amount'] = $netProfitAfterTax + $initialAmountLabaRugiSdTahunLalu + $initialAmountLabaRugiTahunBerjalan;
                // }
            } elseif ($this->report == self::BALANCE_SHEET && $x['coa_id'] == $coaIdLabaRugiTahunBerjalan) {
                // $x['amount'] = $startBalance['balance'];

                // if (empty($yearConfig) || $year >= $yearConfig) {

                    $incomeStatement = IncomeStatement::createFromDateRange($this->dateFrom, $this->dateTo);
                    $incomeStatement = $incomeStatement->get();
                    $x['amount'] = $incomeStatement['net_profit_after_tax'];
                // }
            }

            return $x;
        });

        $treeManipulator = new TreeManipulator($coaList->toArray(), 'coa_id', 'coa_id_parent');
        $treeManipulator->generate();
        $treeManipulator->generatePosition();

        return $treeManipulator->getNewList();
    }
}
