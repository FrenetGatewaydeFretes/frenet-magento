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
 * Class Frenet_Shipping_Model_Config_Source_Product_Quote_Product_Types
 */
class Frenet_Shipping_Model_Config_Source_Product_Quote_Product_Types
{
    /**
     * @var array
     */
    private $options = [];

    /**
     * @var Frenet_Shipping_Helper_Data
     */
    private $helper;

    public function __construct()
    {
        $this->helper = Mage::helper('frenet_shipping');
    }

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
            ProductType::TYPE_SIMPLE       => $this->helper->__('Simple Products'),
            ProductType::TYPE_CONFIGURABLE => $this->helper->__('Configurable Products'),
            ProductType::TYPE_BUNDLE       => $this->helper->__('Bundle Products'),
            ProductType::TYPE_GROUPED      => $this->helper->__('Grouped Products'),
        ];

        return $this->options;
    }
}
