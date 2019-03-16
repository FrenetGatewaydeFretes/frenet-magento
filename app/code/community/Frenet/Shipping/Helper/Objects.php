<?php

/**
 * Class Frenet_Shipping_Helper_Objects
 */
class Frenet_Shipping_Helper_Objects
{
    /**
     * @return Mage_Core_Model_Cache
     */
    public function cache()
    {
        return Mage::app()->getCacheInstance();
    }

    /**
     * @return Frenet_Shipping_Model_Cache_Manager
     */
    public function cacheManager()
    {
        return $this->getInstance('frenet_shipping/cache_manager');
    }

    /**
     * @return Frenet_Shipping_Model_SerializerInterface
     */
    public function serializer()
    {
        return $this->getInstance('frenet_shipping/serializer');
    }

    /**
     * @return Frenet_Shipping_Model_CalculatorInterface
     */
    public function calculator()
    {
        return $this->getInstance('frenet_shipping/calculator');
    }

    /**
     * @return Frenet_Shipping_Model_Config
     */
    public function config()
    {
        return $this->getInstance('frenet_shipping/config');
    }

    /**
     * @return Frenet_Shipping_Model_Quote_Item_Validator
     */
    public function quoteItemValidator()
    {
        return $this->getInstance('frenet_shipping/quote_item_validator');
    }

    /**
     * @return Frenet_Shipping_Model_Quote_Item_Quantity_CalculatorInterface
     */
    public function quoteItemQtyCalculator()
    {
        return $this->getInstance('frenet_shipping/quote_item_quantity_calculator');
    }

    /**
     * @return Frenet_Shipping_Model_Catalog_Product_Attributes_Mapping
     */
    public function productAttributesMapping()
    {
        return $this->getInstance('frenet_shipping/catalog_product_attributes_mapping');
    }

    /**
     * @return Frenet_Shipping_Model_Catalog_Product_Dimensions_Extractor
     */
    public function productDimensionsExtrator()
    {
        return $this->getInstance('frenet_shipping/catalog_product_dimensions_extractor');
    }

    /**
     * @return Frenet_Shipping_Model_Weight_Converter
     */
    public function weightConverter()
    {
        return $this->getInstance('frenet_shipping/weight_converter');
    }

    /**
     * @return Frenet_Shipping_Model_Service_Api
     */
    public function apiService()
    {
        return $this->getInstance('frenet_shipping/service_api');
    }

    /**
     * @return Frenet_Shipping_Model_Tracking
     */
    public function trackingService()
    {
        return $this->getInstance('frenet_shipping/tracking');
    }

    /**
     * @return Frenet_Shipping_Model_Service_Finder|Mage_Core_Model_Abstract
     */
    public function serviceFinder()
    {
        return $this->getInstance('frenet_shipping/service_finder');
    }

    /**
     * @param string $modelClass
     * @param array  $arguments
     * @param bool   $singleton
     *
     * @return false|object|mixed
     */
    private function getInstance($modelClass, array $arguments = array(), $singleton = true)
    {
        if (true === $singleton) {
            return Mage::getSingleton($modelClass, $arguments);
        }

        return Mage::getModel($modelClass, $arguments);
    }
}
