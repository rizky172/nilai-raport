<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Libs\Libs;

class QtyCalculator
{
    private $detail;
    private $used;
    private $retur;

    function getDetail()
    {
        return $this->detail;
    }

    function setDetail($detail)
    {
        $this->detail = $detail;
    }

    function getUsed()
    {
        return $this->used;
    }

    function setUsed($used)
    {
        $this->used = $used;
    }

    function getRetur()
    {
        return $this->retur;
    }

    function setRetur($retur)
    {
        $this->retur = $retur;
    }

    public function addDetail($id, $qty)
    {
        $this->detail[] = [
            'id' => $id,
            'qty' => $qty
        ];
    }

    public function addUsed($id, $qty, $position = null)
    {
        $this->used[] = [
            'id' => $id,
            'qty' => $qty,
            'position' => $position
        ];
    }

    public function addRetur($id, $qty, $position = null)
    {
        $this->retur[] = [
            'id' => $id,
            'qty' => $qty,
            'position' => $position
        ];
    }

    public function getTotalRetur($id, $ignorePosition = null)
    {
        $coll = collect($this->retur)
            ->where('id', $id);

        if ($ignorePosition !== null)
            $coll = $coll->where('position', '!=', $ignorePosition);

        return $coll->sum('qty');
    }

    public function getTotalUsed($id, $ignorePosition = null)
    {
        $coll = collect($this->used)
            ->where('id', $id);

        if ($ignorePosition !== null)
            $coll = $coll->where('position', '!=', $ignorePosition);

        return $coll->sum('qty');
    }

    public function getTotalQty($id)
    {
        return collect($this->detail)->where('id', $id)->sum('qty');
    }

    public function getTotalAvailable($id, $ignorePosition = null)
    {
        $total = $this->getTotalQty($id);
        $used = $this->getTotalUsed($id, $ignorePosition);
        $retur = $this->getTotalRetur($id, $ignorePosition);

        return  $total - $used + $retur;
    }
}
