<?php
namespace App\Libs\Libs;

class PriceCalculator
{
    private $basePrice;
    private $quotePrice;
    private $precision;

    public function __construct($basePrice, $quotePrice, $step)
    {
        $precision = strlen(substr(strrchr((float) $step, "."), 1));

        $this->basePrice = $basePrice;
        $this->quotePrice = $quotePrice;
        $this->precision = $precision;
    }

    private function countBaseAsset()
    {
        // get price
        $tmp = ((1 / $this->basePrice) * $this->quotePrice);
        // fix price
        // $result = $this->fixPrecision($tmp);

        $round = self::roundUp($tmp, $this->precision);

        $pow = pow(10, $this->precision);

        $result = $round - (1 / $pow);

        return $result;
        // return $round;
    }

    private function countQuoteAsset()
    {
        $qty = $this->countBaseAsset();

        $result = $qty / (1 / $this->basePrice);

        return self::roundUp($result, $this->precision);
    }

    private function countPrecision()
    {
        return self::roundUp($this->basePrice, $this->precision);
    }

    private function fixPrecision($tmp)
    {
        // check convert price before
        $tmpResult = $this->basePrice * self::roundUp($tmp, $this->precision);

        $result = $tmp;
        if ($tmpResult < $this->quotePrice) {
            // check different price from quote_price and result
            $diff = $this->quotePrice - $tmpResult;
            // get base price from different price
            $diffPrice = ((1 / $this->basePrice) * $diff);
            // add diff price to tmp price
            $result = $diffPrice + $tmp;
        }

        return $result;
    }

    private static function roundUp($value, $precision)
    {
        $pow = pow(10, $precision);

        return (ceil($pow * $value) + ceil($pow * $value - ceil($pow * $value))) / $pow;
    }

    public static function getBaseAsset($basePrice, $quotePrice, $precision = 0.01)
    {
        $repo = new Self($basePrice, $quotePrice, $precision);

        return $repo->countBaseAsset();
    }

    public static function getQuoteAsset($basePrice, $quotePrice, $precision = 0.01)
    {
        $repo = new Self($basePrice, $quotePrice, $precision);

        return $repo->countQuoteAsset();
    }

    public static function getPrecision($price, $precision = 0.01)
    {
        $repo = new Self($price, null, $precision);

        return $repo->countPrecision();
    }

    public static function getSellQty($qty, $precision = 0.01)
    {
        return $qty - $precision;
    }
}
