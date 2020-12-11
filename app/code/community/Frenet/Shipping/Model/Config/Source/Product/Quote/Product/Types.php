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
    use Frenet_Shipping_Helper_ObjectsTrait;

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
            ProductType::TYPE_SIMPLE       => $this->objects()->helper()->__('Simple Products'),
            ProductType::TYPE_CONFIGURABLE => $this->objects()->helper()->__('Configurable Products'),
            ProductType::TYPE_BUNDLE       => $this->objects()->helper()->__('Bundle Products'),
            ProductType::TYPE_GROUPED      => $this->objects()->helper()->__('Grouped Products'),
        ];

        return $this->options;
    }
}
