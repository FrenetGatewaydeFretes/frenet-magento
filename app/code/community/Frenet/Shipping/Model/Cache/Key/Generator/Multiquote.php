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

class Frenet_Shipping_Model_Cache_Key_Generator_Multiquote
    implements Frenet_Shipping_Model_Cache_Key_GeneratorInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Frenet_Shipping_Model_Config
     */
    private $config;

    public function __construct()
    {
        $this->config = $this->objects()->config();
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        return $this->config->isMultiQuoteEnabled() ? 'multi' : 'single';
    }
}
