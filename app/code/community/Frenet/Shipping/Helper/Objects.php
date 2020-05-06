<?php

/**
 * Class Frenet_Shipping_Helper_Objects
 */
class Frenet_Shipping_Helper_Objects
{
    /**
     * @var string
     */
    private $defaultClassGroup = 'frenet_shipping';

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
        return $this->getInstance('cache_manager');
    }

    /**
     * @return Frenet_Shipping_Model_SerializerInterface
     */
    public function serializer()
    {
        return $this->getInstance('serializer');
    }

    /**
     * @return Frenet_Shipping_Model_CalculatorInterface
     */
    public function calculator()
    {
        return $this->getInstance('calculator');
    }

    /**
     * @return Frenet_Shipping_Model_Config
     */
    public function config()
    {
        return $this->getInstance('config');
    }

    /**
     * @return Frenet_Shipping_Model_Quote_Coupon_Processor
     */
    public function quoteCouponProcessor()
    {
        return $this->getInstance('quote_coupon_processor');
    }

    /**
     * @return Frenet_Shipping_Model_Quote_Multi_Quote_Validator
     */
    public function quoteMultiQuoteValidator()
    {
        return $this->getInstance('quote_multi_quote_validator');
    }

    /**
     * @return Frenet_Shipping_Model_Quote_Item_Validator
     */
    public function quoteItemValidator()
    {
        return $this->getInstance('quote_item_validator');
    }

    /**
     * @return Frenet_Shipping_Model_Quote_Item_Quantity_CalculatorInterface
     */
    public function quoteItemQtyCalculator()
    {
        return $this->getInstance('quote_item_quantity_calculator');
    }

    /**
     * @return Frenet_Shipping_Model_Quote_Item_Price_Calculator
     */
    public function quoteItemPriceCalculator()
    {
        return $this->getInstance('quote_item_price_calculator');
    }

    /**
     * @return Frenet_Shipping_Model_Quote_Item_Price_Calculator_Factory
     */
    public function quoteItemPriceCalculatorFactory()
    {
        return $this->getInstance('quote_item_price_calculator_factory');
    }

    /**
     * @return Frenet_Shipping_Model_Catalog_Product_Attributes_Mapping
     */
    public function productAttributesMapping()
    {
        return $this->getInstance('catalog_product_attributes_mapping');
    }

    /**
     * @return Frenet_Shipping_Model_Catalog_Product_Dimensions_Extractor
     */
    public function productDimensionsExtractor()
    {
        return $this->getInstance('catalog_product_dimensions_extractor');
    }

    /**
     * @return Frenet_Shipping_Model_Catalog_Product_Category_Extractor
     */
    public function productCategoryExtractor()
    {
        return $this->getInstance('catalog_product_category_extractor');
    }

    /**
     * @return Frenet_Shipping_Model_Weight_Converter
     */
    public function weightConverter()
    {
        return $this->getInstance('weight_converter');
    }

    /**
     * @return Frenet_Shipping_Model_Service_Api
     */
    public function apiService()
    {
        return $this->getInstance('service_api');
    }

    /**
     * @return Frenet_Shipping_Model_Tracking
     */
    public function trackingService()
    {
        return $this->getInstance('tracking');
    }

    /**
     * @return Frenet_Shipping_Model_Service_Finder|Mage_Core_Model_Abstract
     */
    public function serviceFinder()
    {
        return $this->getInstance('service_finder');
    }

    /**
     * @return Frenet_Shipping_Model_Packages_Package
     */
    public function package()
    {
        return $this->getInstance('packages_package');
    }

    /**
     * @return Frenet_Shipping_Model_Packages_Package_Factory
     */
    public function packageFactory()
    {
        return $this->getInstance('packages_package_factory');
    }

    /**
     * @return Frenet_Shipping_Model_Packages_Package_Manager
     */
    public function packageManager()
    {
        return $this->getInstance('packages_package_manager');
    }

    /**
     * @return Frenet_Shipping_Model_Packages_Package_Calculator
     */
    public function packageCalculator()
    {
        return $this->getInstance('packages_package_calculator');
    }

    /**
     * @return Frenet_Shipping_Model_Packages_Package_Matching
     */
    public function packageMatching()
    {
        return $this->getInstance('packages_package_matching');
    }

    /**
     * @return Frenet_Shipping_Model_Packages_Package_Limit
     */
    public function packageLimit()
    {
        return $this->getInstance('packages_package_limit');
    }

    /**
     * @return Frenet_Shipping_Model_Packages_Package_Processor
     */
    public function packageProcessor()
    {
        return $this->getInstance('packages_package_processor');
    }

    /**
     * @return Frenet_Shipping_Model_Packages_Package_Item
     */
    public function packageItem()
    {
        return $this->getInstance('packages_package_item');
    }

    /**
     * @return Frenet_Shipping_Model_Packages_Package_Item_Factory
     */
    public function packageItemFactory()
    {
        return $this->getInstance('packages_package_item_factory');
    }

    /**
     * @return Frenet_Shipping_Model_Packages_Package_Item_Distributor
     */
    public function packageItemDistributor()
    {
        return $this->getInstance('packages_package_item_distributor');
    }

    /**
     * @return Frenet_Shipping_Model_Store_Management
     */
    public function storeManagement()
    {
        return $this->getInstance('store_management');
    }

    /**
     * @return Frenet_Shipping_Model_Factory_Product_Resource
     */
    public function productResourceFactory()
    {
        return $this->getInstance('factory_product_resource');
    }

    /**
     * @return Frenet_Shipping_Model_Formatters_Postcode_Normalizer
     */
    public function postcodeNormalizer()
    {
        return $this->getInstance('formatters_postcode_normalizer');
    }

    /**
     * @return Frenet_Shipping_Model_Validator_Postcode
     */
    public function postcodeValidator()
    {
        return $this->getInstance('validator_postcode');
    }

    /**
     * @return Frenet_Shipping_Model_Delivery_Time_Calculator
     */
    public function deliveryTimeCalculator()
    {
        return $this->getInstance('delivery_time_calculator');
    }

    /**
     * @return Frenet_Shipping_Model_Rate_Request_Service
     */
    public function rateRequestService()
    {
        return $this->getInstance('rate_request_service');
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
        if ($this->canAppendDefaultClassGroup($modelClass)) {
            $modelClass = $this->defaultClassGroup . '/' . $modelClass;
        }

        if (true === $singleton) {
            return Mage::getSingleton($modelClass, $arguments);
        }

        return Mage::getModel($modelClass, $arguments);
    }

    /**
     * @param $classGroup
     *
     * @return bool
     */
    private function canAppendDefaultClassGroup($classGroup)
    {
        if (strpos($classGroup, '/')) {
            return false;
        }

        if (strpos($classGroup, $this->defaultClassGroup)) {
            return false;
        }

        return true;
    }
}
