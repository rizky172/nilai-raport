<?php
namespace App\Libs\Accounting;

use Illuminate\Support\Facades\Validator;
use App\Libs\Repository\AbstractRepository;

use App\Config;

/*
 * Generate balance sheet
 */
class IncomeStatement
{
    private $journalEntry;

    /*
     * @param JournalEntry Must be correct journal entry configuration. Use
     *  factory pattern ::createFromDate() to help create this class
     */
    public function __construct(JournalEntry $journalEntry)
    {
        $this->journalEntry = $journalEntry;
    }

    /*
     * Crate this class based on \DateTime
     *
     * @param string $dateFrom
     * @param string $dateTo
     *
     * @return IncomeStatement
     */
    public static function createFromDateRange($dateFrom, $dateTo)
    {
        $yearDateFrom = !empty($dateFrom) ? date("Y", strtotime($dateFrom)) : null;
        $yearDateTo = !empty($dateTo) ? date("Y", strtotime($dateTo)) : null;
        $yearConfig = Config::where('key', 'coa_id_tahun_mulai')
                            ->first()
                            ->value ?? 0;

        $fields = [
            'year_config' => $yearConfig,
            'year_date_from' => $yearDateFrom,
            'year_date_to' => $yearDateTo
        ];

        $rules = [
            // 'year_date_from' => 'nullable|numeric|gte:year_config',
            'year_date_to' => 'required'
        ];

        $messages = [
            // 'year_date_from.required' => 'Tanggal dari wajib diisi',
            'year_date_to.required' => 'Tanggal ke wajib diisi',
            'year_date_from.gte' => sprintf('Tanggal ke harus lebih besar atau sama dengan tahun mulai pada pengaturan (%s)', $yearConfig),
            'year_date_to.gte' => sprintf('Tanggal ke harus lebih besar atau sama dengan tahun mulai pada pengaturan (%s)', $yearConfig)
        ];

        $validator = Validator::make($fields, $rules, $messages);
        AbstractRepository::validOrThrow($validator);

        $journal = new JournalEntry(JournalEntry::INCOME_STATEMENT, $dateFrom, $dateTo);
        return new self($journal);
    }

    /*
     * Get structured array that ready to show into HTML view
     *
     * @return array
     */
    public function get()
    {
        $journal = collect($this->journalEntry->get());
        // var_dump($journal->toArray()); die;
        $journal = $journal->map(function($x) {
            if(in_array($x['group_by'],
                explode(',', 'cogs,operating-expenses,other-expenses,income-tax'))) {
                $x['total'] = $x['total'] * -1;
            }

            $x['coa_name'] = $x['label'];
            return $x;
        });

        $data = [];
        $detail = $journal->where('total', '!=', 0);
        $detail = $detail->sortByDesc('position');

        $data['revenues'] = $detail->where('group_by', 'revenues')
                                        ->values()
                                        ->all();
        $data['cogs'] = $detail->where('group_by', 'cogs')
                                        ->values()
                                        ->all();
        $data['operating_expenses'] = $detail->where('group_by', 'operating-expenses')
                                        ->values()
                                        ->all();
        $data['other_income'] = $detail->where('group_by', 'other-income')
                                        ->values()
                                        ->all();
        $data['other_expenses'] = $detail->where('group_by', 'other-expenses')
                                        ->values()
                                        ->all();
        $data['income_tax'] = $detail->where('group_by', 'income-tax')
                                        ->values()
                                        ->all();

        $data['total_revenues'] = $journal->where('group_by', 'revenues')
            ->where('level', 0)
            ->sum('total');
        $data['total_cogs'] = $journal->where('group_by', 'cogs')
            ->where('level', 0)
            ->sum('total');
        $data['total_operating_expenses'] = $journal->where('group_by', 'operating-expenses')
            ->where('level', 0)
            ->sum('total');
        $data['total_other_income'] = $journal->where('group_by', 'other-income')
            ->where('level', 0)
            ->sum('total');
        $data['total_other_expenses'] = $journal->where('group_by', 'other-expenses')
            ->where('level', 0)
            ->sum('total');
        $data['total_income_tax'] = $journal->where('group_by', 'income-tax')
            ->where('level', 0)
            ->sum('total');

        $data['gross_profit'] = $data['total_revenues'] - $data['total_cogs'];
        $data['operating_profit'] = $data['gross_profit'] - $data['total_operating_expenses'];
        $data['net_profit_before_tax'] = $data['operating_profit'] + $data['total_other_income'] - $data['total_other_expenses'];
        $data['net_profit_after_tax'] = $data['net_profit_before_tax'] - $data['total_income_tax'];

        return $data;
    }
}
