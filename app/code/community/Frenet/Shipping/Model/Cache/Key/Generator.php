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

class Frenet_Shipping_Model_Cache_Key_Generator
    implements Frenet_Shipping_Model_Cache_Key_GeneratorInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var array
     */
    private $generators;

    /**
     * @var Frenet_Shipping_Model_SerializerInterface
     */
    private $serializer;

    public function __construct()
    {
        $this->generators = [
            'postcode'        => $this->objects()->cacheKeyGeneratorPostcode(),
            'quote_item'      => $this->objects()->cacheKeyGeneratorQuoteItem(),
            'discount_coupon' => $this->objects()->cacheKeyGeneratorCoupon(),
            'multi_quote'     => $this->objects()->cacheKeyGeneratorMultiQuote(),
        ];
        $this->serializer = $this->objects()->serializer();
    }

    public function generate()
    {
        $cacheKey = [];

        /** @var Frenet_Shipping_Model_Cache_Key_GeneratorInterface $generator */
        foreach ($this->generators as $generator) {
            $cacheKey[] = $generator->generate();
        }

        return $this->serializer->serialize($cacheKey);
    }
}
