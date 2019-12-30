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

class Frenet_Shipping_Model_Catalog_Product_Attributes_Mapping
    implements Frenet_Shipping_Model_Catalog_Product_Attributes_MappingInterface
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
     * {@inheritdoc}
     */
    public function getWeightAttributeCode()
    {
        return $this->config->getWeightAttribute() ?: self::DEFAULT_ATTRIBUTE_WEIGHT;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeightAttributeCode()
    {
        return $this->config->getHeightAttribute() ?: self::DEFAULT_ATTRIBUTE_HEIGHT;
    }

    /**
     * {@inheritdoc}
     */
    public function getLengthAttributeCode()
    {
        return $this->config->getLengthAttribute() ?: self::DEFAULT_ATTRIBUTE_LENGTH;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidthAttributeCode()
    {
        return $this->config->getWidthAttribute() ?: self::DEFAULT_ATTRIBUTE_WIDTH;
    }

    /**
     * {@inheritdoc}
     */
    public function getLeadTimeAttributeCode()
    {
        return self::DEFAULT_ATTRIBUTE_LEAD_TIME;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragileAttributeCode()
    {
        return self::DEFAULT_ATTRIBUTE_FRAGILE;
    }
}
