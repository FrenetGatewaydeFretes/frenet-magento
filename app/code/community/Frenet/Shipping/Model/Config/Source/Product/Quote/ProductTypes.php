<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 * @package  Frenet\Shipping
 *
 * @author   Tiago Sampaio <tiago@tiagosampaio.com>
 * @link     https://github.com/tiagosampaio
 * @link     https://tiagosampaio.com
 *
 * Copyright (c) 2020.
 */

use Frenet_Shipping_Model_Catalog_ProductType as ProductType;

/**
 * Class ProductTypes
 *
 * @package Frenet\Shipping\Model\Config\Source\Catalog\Product\Quote
 */
class Frenet_Shipping_Model_Config_Source_Catalog_Product_Quote_ProductTypes
{
    /**
     * @var array
     */
    private $options = [];

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->toArray() as $code => $label) {
            $options[] = [
                'label' => $label,
                'value' => $code,
            ];
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $this->options = [
            ProductType::TYPE_SIMPLE       => Mage::helper('frenet_shipping')->__('Simple Products'),
            ProductType::TYPE_CONFIGURABLE => Mage::helper('frenet_shipping')->__('Configurable Products'),
            ProductType::TYPE_BUNDLE       => Mage::helper('frenet_shipping')->__('Bundle Products'),
            ProductType::TYPE_GROUPED      => Mage::helper('frenet_shipping')->__('Grouped Products'),
        ];

        return $this->options;
    }
}
