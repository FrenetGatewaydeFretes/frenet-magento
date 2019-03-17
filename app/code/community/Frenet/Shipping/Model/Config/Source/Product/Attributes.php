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
                if (!$this->validateAttribute($attribute)) {
                    continue;
                }

                $this->options[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
            }
        }

        return $this->options;
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return bool
     */
    private function validateAttribute(Mage_Catalog_Model_Resource_Eav_Attribute $attribute)
    {
        if (!$attribute->getAttributeCode()) {
            return false;
        }

        if (!$attribute->getFrontendLabel()) {
            return false;
        }

        return true;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    private function getProductAttributes()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $collection */
        $collection = Mage::getResourceModel('catalog/product_attribute_collection');
        return $collection;
    }
}
