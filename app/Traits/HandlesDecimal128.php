<?php

namespace App\Traits;

use MongoDB\BSON\Decimal128;

trait HandlesDecimal128
{
    /**
     * Convert Decimal128 attributes to float when accessed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        // Convert Decimal128 to float for numeric fields
        if ($value instanceof Decimal128) {
            return (float) $value->__toString();
        }

        return $value;
    }

    /**
     * Convert Decimal128 attributes to float in array representation
     */
    public function toArray()
    {
        $array = parent::toArray();

        foreach ($array as $key => $value) {
            if ($value instanceof Decimal128) {
                $array[$key] = (float) $value->__toString();
            } elseif (is_array($value)) {
                // Recursivamente convertir Decimal128 en arrays anidados
                $array[$key] = $this->convertDecimal128InArray($value);
            }
        }

        return $array;
    }

    /**
     * Recursively convert Decimal128 values in nested arrays
     */
    protected function convertDecimal128InArray($array)
    {
        foreach ($array as $key => $value) {
            if ($value instanceof Decimal128) {
                $array[$key] = (float) $value->__toString();
            } elseif (is_array($value)) {
                $array[$key] = $this->convertDecimal128InArray($value);
            }
        }

        return $array;
    }

    /**
     * Convert specific attribute to float if it's Decimal128
     */
    public function toFloat($attribute)
    {
        $value = $this->getAttribute($attribute);

        if ($value instanceof Decimal128) {
            return (float) $value->__toString();
        }

        return (float) $value;
    }
}