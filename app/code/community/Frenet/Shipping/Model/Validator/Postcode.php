<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 *
 * @author   Tiago Sampaio <tiago@tiagosampaio.com>
 * @link     https://github.com/tiagosampaio
 * @link     https://tiagosampaio.com
 *
 * Copyright (c) 2020.
 */

/**
 * Class Postcode
 **/
class Frenet_Shipping_Model_Validator_Postcode
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var \Frenet\Shipping\Model\Formatters\PostcodeNormalizer
     */
    private $postcodeNormalizer;

    public function __construct()
    {
        /** @var Frenet_Shipping_Model_Formatters_Postcode_Normalizer postcodeNormalizer */
        $this->postcodeNormalizer = $this->objects()->postcodeNormalizer();
    }

    /**
     * @param string|null $postcode
     *
     * @return bool
     */
    public function validate($postcode = null)
    {
        if (empty($postcode)) {
            return false;
        }

        if (!((int) $this->postcodeNormalizer->format($postcode))) {
            return false;
        }

        return true;
    }
}
