<?php

/**
 * Class Frenet_Shipping_Model_Config_Source_Product_Attributes
 */
class Frenet_Shipping_Model_Config_Source_Product_Attributes
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
            foreach ($this->getProductAttributes() as $attribute) {
                $this->options[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
            }
        }

        return $this->options;
    }

    /**
     * @return array
     */
    private function getProductAttributes()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();

        return $attributes;
    }
}
