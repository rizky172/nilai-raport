<?php
namespace App\Libs\Accounting;

use Illuminate\Support\Facades\Validator;
use App\Libs\Repository\AbstractRepository;

use App\Config;

/*
 * Generate balance sheet
 */
class BalanceSheet
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
     * @return BalanceSheet
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
            'year_date_from' => 'required|numeric|gte:year_config',
            'year_date_to' => 'required|numeric|gte:year_config'
        ];

        $messages = [
            'year_date_from.required' => 'Tanggal dari wajib diisi',
            'year_date_to.required' => 'Tanggal ke wajib diisi',
            'year_date_from.gte' => sprintf('Tanggal dari harus lebih besar atau sama dengan tahun mulai pada pengaturan (%s)', $yearConfig),
            'year_date_to.gte' => sprintf('Tanggal ke harus lebih besar atau sama dengan tahun mulai pada pengaturan (%s)', $yearConfig)
        ];

        $validator = Validator::make($fields, $rules, $messages);
        AbstractRepository::validOrThrow($validator);
        
        $journal = new JournalEntry(JournalEntry::BALANCE_SHEET, $dateFrom, $dateTo);
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
                explode(',', 'liabilities,equity'))) {
                $x['total'] = $x['total'] * -1;
            }

            $x['coa_name'] = $x['label'];

            return $x;
        });

        $data = [];
        $detail = $journal->where('total', '!=', 0);
        $detail = $detail->sortByDesc('position');

        $data['assets'] = $detail->where('group_by', 'assets')
                                    ->values()
                                    ->all();
        $data['liabilities'] = $detail->where('group_by', 'liabilities')
                                    ->values()
                                    ->all();
        $data['equity'] = $detail->where('group_by', 'equity')
                                    ->values()
                                    ->all();

        $data['total_assets'] = $journal->where('group_by', 'assets')
            ->where('level', 0)
            ->sum('total');
        $data['total_liabilities'] = $journal->where('group_by', 'liabilities')
            ->where('level', 0)
            ->sum('total');
        $data['total_equity'] = $journal->where('group_by', 'equity')
            ->where('level', 0)
            ->sum('total');

        return $data;
    }
}
