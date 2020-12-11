<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 *
 * @author Tiago Sampaio <tiago@tiagosampaio.com>
 * @link https://github.com/tiagosampaio
 * @link https://tiagosampaio.com
 *
 * Copyright (c) 2020.
 */

/**
 * Class Frenet_Shipping_Model_Formatters_Postcode_Normalizer
 */
class Frenet_Shipping_Model_Formatters_Postcode_Normalizer
{
    /**
     * @param string $postcode
     *
     * @return string
     */
    public function format($postcode = null)
    {
        $postcode = preg_replace('/[^0-9]/', null, $postcode);
        $postcode = str_pad($postcode, 8, '0', STR_PAD_LEFT);

        return $postcode;
    }
}
