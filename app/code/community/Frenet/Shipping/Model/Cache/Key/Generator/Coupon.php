<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 * @package  Frenet_Shipping
 * @author   Tiago Sampaio <tiago@tiagosampaio.com>
 * @link     https://github.com/tiagosampaio
 * @link     https://tiagosampaio.com
 *
 * Copyright (c) 2019.
 */

class Frenet_Shipping_Model_Cache_Key_Generator_Coupon
    implements Frenet_Shipping_Model_Cache_Key_GeneratorInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Frenet_Shipping_Model_Quote_Coupon_Processor
     */
    private $couponProcessor;

    public function __construct()
    {
        $this->couponProcessor = $this->objects()->quoteCouponProcessor();
    }

    /**
     * @ingeritDoc
     */
    public function generate()
    {
        return $this->couponProcessor->getCouponCode() ?: 'no-coupon';
    }
}
