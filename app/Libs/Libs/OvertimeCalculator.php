<?php

namespace App\Libs\Libs;

/**
 * Paid leave calculator.
 */
class OvertimeCalculator
{
    private $hours;
    private $isNormalDay;
    public function __construct($hours)
    {
       $this->hours = $hours;
    }

    public function isNormalDay($boolean)
    {
       $this->isNormalDay = $boolean;
    }

    public function getTotal()
    {
        $total = 0;
        if ($this->isNormalDay == false) {
            if ($this->hours < 8) {
                $total = $this->hours * 2;
            } else if ($this->hours >= 8 && $this->hours < 9) {
                $total = (7 * 2) + 3;
            } else if ($this->hours >= 9) {
                $total = (7 * 2) + 3 + (($this->hours - 8) * 4);
            }
        }
        elseif ($this->isNormalDay == true) {
            if ($this->hours < 8) {
                $total = $this->hours * 2 - 0.5;
            } else if ($this->hours >= 8 && $this->hours < 9) {
                $total = (7 * 2) - 0.5 + 3;
            } else if ($this->hours >= 9) {
                // $total = (7 * 2)+ 3 +(($this->hours - 8) * 4);
                // formula from excel ((7*2)-0.5)+3+((E7-8)*4)
                $total = ((7 * 2) - 0.5) + 3 +(($this->hours - 8) * 4);
            }
        }

        return round($total, 2);
    }
}
