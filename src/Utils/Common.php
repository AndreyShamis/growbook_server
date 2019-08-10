<?php


namespace App\Utils;


class Common
{
    public static function percentage($total, $number): float
    {
        if ($total === 0) {
            return 0;
        }
        return ((100 * $number) / $total);
    }

    public static function percentChange($number1, $number2): float
    {
        $ret = 0;
        try{
            $total = max($number1, $number2);
            $number = min($number1, $number2);
            $p = self::percentage($total, $number);
            if ($p > 100) {
                $ret = -1 * ($p - 100);
            } else {
                $ret = 100 - $p;
            }
        } catch (\Throwable $ex) {}
        return round($ret, 1);
    }
}