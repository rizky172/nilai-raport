<?php

namespace App\Libs\Libs;

/**
 * Paid leave date. Make expired paid leave last year that doesn't used
 */
class PaidLeaveDateMatcher
{
    private $list = [];

    /*
     * @param int Date you want to check
     * @param array list paid_leave_date
     *  [
     *      [ 'date' => \DateTime, 'category' => string, 'notes' => string ]
     *  ]
     */
    public function __construct($list)
    {
        $this->list = $list;
    }

    private function validate()
    {
        // Validation
        $fields = [
            'list' => $this->list->toArray(),
            'last_year' => $this->lastYear
        ];

        $rules = array(
            'list' => 'required|array',
            'last_year' => 'required',

            'last_year.*.date' => 'required|date_format:Y-m-d',
            'last_year.*.category' => 'required|in:yearly,once'
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
    public function get(\DateTime $date)
    {
        $list = collect($this->list);
        // Do for once first
        $list = $list->sortBy('category');
        // Match date
        $filtered = $list->filter(function($x) use ($date) {
                    $tempDate = new \DateTime($x['date']);

                    $temp = false;
                    switch($x['category']) {
                        case 'once':
                            $temp = $date->format('Ymd') == $tempDate->format('Ymd');
                            break;

                        case 'yearly':
                            $temp = $date->format('md') == $tempDate->format('md');
                            break;
                    }

                    return $temp;
                });

        return $filtered->first();
    }
}
