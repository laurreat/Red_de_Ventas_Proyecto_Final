<?php

if (!function_exists('format_number')) {
    function format_number($number, $decimals = 0) {
        return number_format((float)($number ?? 0), $decimals, ',', '.');
    }
}

if (!function_exists('to_float')) {
    function to_float($value) {
        return (float) preg_replace('/[^0-9.\-]/', '', $value ?? 0);
    }
}
