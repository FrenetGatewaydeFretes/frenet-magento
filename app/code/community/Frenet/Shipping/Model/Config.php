<?php

/**
 * Class Frenet_Shipping_Model_Config
 */
class Frenet_Shipping_Model_Config
{
    /**
     * @return bool
     */
    public function isActive($store = null)
    {
        return (bool) $this->getCarrierConfig('active', $store);
    }

    /**
     * @return string
     */
    public function getToken($store = null)
    {
        return $this->getCarrierConfig('token', $store);
    }

    /**
     * @return string
     */
    public function getWeightAttribute($store = null)
    {
        return $this->getCarrierConfig('attributes_mapping_weight', $store);
    }

    /**
     * @return string
     */
    public function getHeightAttribute($store = null)
    {
        return $this->getCarrierConfig('attributes_mapping_height', $store);
    }

    /**
     * @return string
     */
    public function getLengthAttribute($store = null)
    {
        return $this->getCarrierConfig('attributes_mapping_length', $store);
    }

    /**
     * @return string
     */
    public function getWidthAttribute($store = null)
    {
        return $this->getCarrierConfig('attributes_mapping_width', $store);
    }

    /**
     * @return float
     */
    public function getDefaultWeight($store = null)
    {
        return (float) $this->getCarrierConfig('default_measurements_default_weight', $store);
    }

    /**
     * @return float
     */
    public function getDefaultHeight($store = null)
    {
        return (float) $this->getCarrierConfig('default_measurements_default_height', $store);
    }

    /**
     * @return float
     */
    public function getDefaultLength($store = null)
    {
        return (float) $this->getCarrierConfig('default_measurements_default_length', $store);
    }

    /**
     * @return float
     */
    public function getDefaultWidth($store = null)
    {
        return (float) $this->getCarrierConfig('default_measurements_default_width', $store);
    }

    /**
     * @return int
     */
    public function getAdditionalLeadTime($store = null)
    {
        return (int) $this->getCarrierConfig('additional_lead_time', $store);
    }

    /**
     * @return bool
     */
    public function canShowShippingForecast($store = null)
    {
        return (bool) $this->getCarrierConfig('show_shipping_forecast', $store);
    }

    /**
     * @return bool
     */
    public function getShippingForecast($store = null)
    {
        return (string) $this->getCarrierConfig('shipping_forecast_message', $store);
    }

    /**
     * @param string|int|Mage_Core_Model_Store $store
     *
     * @return bool
     */
    public function isMultiQuoteEnabled($store = null)
    {
        return (bool) $this->getCarrierConfig('multi_quote', $store);
    }

    /**
     * @return bool
     */
    public function isDebugModeEnabled($store = null)
    {
        return (bool) $this->getCarrierConfig('debug', $store);
    }

    /**
     * @return string
     */
    public function getDebugFilename($store = null)
    {
        return (string) $this->getCarrierConfig('debug_filename', $store);
    }

    /**
     * @param string|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getOriginPostcode($store = null)
    {
        return $this->get('shipping', 'origin', 'postcode', $store);
    }

    /**
     * @param string                           $field
     * @param string|int|Mage_Core_Model_Store $store
     *
     * @return mixed
     */
    public function getCarrierConfig($field, $store = null)
    {
        return $this->get('carriers', Frenet_Shipping_Model_Carrier_Frenet::CARRIER_CODE, $field, $store);
    }

    /**
     * @param string                           $section
     * @param string                           $group
     * @param string                           $field
     * @param string|int|Mage_Core_Model_Store $store
     *
     * @return mixed
     */
    public function get($section, $group, $field, $store = null)
    {
        $path = implode('/', array($section, $group, $field));
        return Mage::getStoreConfig($path, $this->getStore($store));
    }

    /**
     * @param null|string|int|Mage_Core_Model_Store $store
     * @return Mage_Core_Model_Store
     */
    private function getStore($store = null)
    {
        return Mage::getStoreConfig($store);
    }
}
