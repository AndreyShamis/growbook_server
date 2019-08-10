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
        try{
            $total = max($number1, $number2);
            $number = min($number1, $number2);
            $p = self::percentage($total, $number);
            if ($p > 100) {
                return $p - 100;
            }
            return 100 - $p;
        } catch (\Throwable $ex) {}
        return 0;
    }
}