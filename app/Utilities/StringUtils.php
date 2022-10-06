<?php

namespace App\Utilities;

class StringUtils {

    public static function getDeclinedString(int $num, array $variants, bool $addNum = true): string {
        $m = $num % 10;
        $j = $num % 100;
        if ($m == 1) $s = $variants[0];
        if ($m >= 2 && $m <= 4) $s = $variants[1];
        if ($m == 0 || $m >= 5 || ($j >= 10 && $j <= 20)) $s = $variants[2];
        return ($addNum ? "$num " : '') . (@$s ?: $variants[0]);
    }

}
