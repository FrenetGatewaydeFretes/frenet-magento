<?php

class Frenet_Shipping_Model_Formatters_Postcode_Normalizer
{
    /**
     * @param string $postcode
     *
     * @return string
     */
    public function format($postcode)
    {
        $postcode = preg_replace('/[^0-9]/', null, $postcode);
        $postcode = str_pad($postcode, 8, '0', STR_PAD_LEFT);

        return $postcode;
    }
}
