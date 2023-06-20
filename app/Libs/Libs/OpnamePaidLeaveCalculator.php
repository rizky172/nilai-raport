<?php

namespace App\Libs\Libs;

/**
 * Opname paid leave. Make expired paid leave last year that doesn't used
 */
class OpnamePaidLeaveCalculator
{
    private $lastYear = 0;
    private $list = [];

    /*
     * @param int How many remaining last year paid leave that haven't used
     * @param array list opname of this year.
     *  [
     *      [ 'date' => \DateTime, 'qty' => int ]
     *  ]
     */
    public function __construct($countLastYear, $listThisYear)
    {
        $this->lastYear = $countLastYear;
        $this->list = $listThisYear;
    }

    private function validate()
    {
        // Validation
        $fields = [
            'list' => $this->list,
            'last_year' => $this->lastYear
        ];

        $rules = array(
            'list' => 'required|array',
            'last_year' => 'required',

            'last_year.*.date' => 'required|date_format:Y-m-d',
            'last_year.*.qty' => 'required|number'
        );

        $validator = Validator::make($fields, $rules, []);

        AbstractRepository::validOrThrow($validator);
    }

    /* Get expired qty that doesn't used.
     *
     * @param \DateTime
     *
     * @return int when negative it means all paid leave used last year.
     */
    public function getExpiredQty(\DateTime $date)
    {
        $dateString = $date->format('d-m');

        if ($dateString != '01-07')
            return 0;

        $list = collect($this->list);
        $count = $list->sum(function($x) use ($date) {
            $d = new \DateTime($x['date']);

            return $d <= $date ? (int) $x['qty'] : 0;
        });

        return $this->lastYear + $count;
    }
}
