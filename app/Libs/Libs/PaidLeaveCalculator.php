<?php

namespace App\Libs\Libs;

/**
 * Paid leave calculator.
 */
class PaidLeaveCalculator
{
    private $joinDate;

    public function __construct($joinDate)
    {
        $this->joinDate = $joinDate;
    }

    public function getPaidLeave($dateTarget)
    {
        $registered = $this->joinDate;
        $dateDiff = $registered->diff($dateTarget);

        $getMonthDiff = $dateDiff->format('%y') * 12 + $dateDiff->format('%m');

        if ($getMonthDiff % 12 == 0) {
            if ($registered->format('n') != 1) {
                if ($getMonthDiff / 12 != 1) {
                    $getMonthDiff = 0;
                } else {
                    if ($getMonthDiff / 12 < 3) {
                        $getMonthDiff = 12;
                    } else {
                        $getMonthDiff = 12 + 3;
                    }
                }
            } else {
                if ($getMonthDiff / 12 < 3) {
                    $getMonthDiff = 12;
                } else {
                    $getMonthDiff = 12 + 3;
                }
            }
        } else {
            if ($getMonthDiff / 12 > 1 && $getMonthDiff / 12 < 2 && $registered->format('n') != 1 && $dateTarget->format('n') == 1) {
                $getMonthDiff = ($getMonthDiff % 12) - 1;
            } else if ($dateTarget->format('n') == 1) {
                if ($getMonthDiff / 12 > 2 && $getMonthDiff / 12 < 3) {
                    $getMonthDiff = 12;
                } else {
                    $getMonthDiff = 12 + 3;
                }
            } else {
                $getMonthDiff = 0;
            }
        }

        return $getMonthDiff;
    }
}
