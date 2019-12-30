<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 * @package  Frenet\Shipping
 * @author   Tiago Sampaio <tiago@tiagosampaio.com>
 * @link     https://github.com/tiagosampaio
 * @link     https://tiagosampaio.com
 *
 * Copyright (c) 2019.
 */

/**
 * Class Frenet_Shipping_Model_Config_Source_Product_Attributes
 */
class Frenet_Shipping_Model_Config_Source_Product_Attributes
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
                'label' => "{$label} [{$code}]",
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
        if (empty($this->options)) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            foreach ($this->getCollection() as $attribute) {
                $this->options[$attribute->getAttributeCode()] = $attribute->getDefaultFrontendLabel();
            }
        }

        return $this->options;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    private function getCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $collection */
        $collection = Mage::getResourceModel('catalog/product_attribute_collection');
        return $collection;
    }
}
