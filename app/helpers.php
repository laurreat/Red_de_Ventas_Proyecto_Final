<?php

if (!function_exists('to_float')) {
    /**
     * Convierte un valor a float, manejando MongoDB Decimal128
     *
     * @param mixed $value
     * @return float
     */
    function to_float($value)
    {
        if ($value instanceof \MongoDB\BSON\Decimal128) {
            return (float) $value->__toString();
        }

        return (float) $value;
    }
}

if (!function_exists('format_currency')) {
    /**
     * Formatea un valor como moneda, manejando MongoDB Decimal128
     *
     * @param mixed $value
     * @param int $decimals
     * @param string $decimal_separator
     * @param string $thousands_separator
     * @return string
     */
    function format_currency($value, $decimals = 0, $decimal_separator = '.', $thousands_separator = ',')
    {
        return number_format(to_float($value), $decimals, $decimal_separator, $thousands_separator);
    }
}

if (!function_exists('format_number')) {
    /**
     * Formatea un número manejando MongoDB Decimal128
     *
     * @param mixed $value
     * @param int $decimals
     * @param string $decimal_separator
     * @param string $thousands_separator
     * @return string
     */
    function format_number($value, $decimals = 0, $decimal_separator = '.', $thousands_separator = ',')
    {
        return number_format(to_float($value), $decimals, $decimal_separator, $thousands_separator);
    }
}

