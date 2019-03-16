<?php

/**
 * Class Frenet_Shipping_Helper_Data
 */
class Frenet_Shipping_Helper_Data
{
    /**
     * @param string $postcode
     *
     * @return string|string[]|null
     */
    public function normalizePostcode($postcode)
    {
        $postcode = preg_replace('/[^0-9]/', null, $postcode);
        $postcode = str_pad($postcode, 8, '0', STR_PAD_LEFT);
        return $postcode;
    }
}
