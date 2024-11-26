<?php

    function float2hex(float $value): string
    {
        $pack = pack('f', $value);
        $hex = '';

        for ($i = strlen($pack) - 1; $i >= 0; --$i) {
            $hex .= str_pad(dechex(ord($pack[$i])), 2, '0', STR_PAD_LEFT);
        }

        return strtoupper($hex);
    }

    function hex2float(string $hex): float
    {
        $dec = hexdec($hex);

        if ($dec === 0) {
            return 0;
        }

        $sup = 1 << 23;
        $x = ($dec & ($sup - 1)) + $sup * ($dec >> 31 | 1);
        $exp = ($dec >> 23 & 0xFF) - 127;
        $sign = ($dec & 0x80000000) ? -1 : 1;

        return $sign * $x * pow(2, $exp - 23);
    }

    function hexRev($hexStr)
    {
        $arr = [];
        $tmp = '';
        for ($i = 0; $i < strlen($hexStr); ++$i) {
            $tmp .= $hexStr[$i];
            if ((($i + 1) % 2) == 0) {
                $arr[] = $tmp;
                $tmp = '';
            }
        }
        $arr = array_reverse($arr);

        return implode('', $arr);
    }
