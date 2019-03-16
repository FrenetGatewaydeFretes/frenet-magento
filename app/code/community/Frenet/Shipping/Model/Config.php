<?php

/**
 * Class Frenet_Shipping_Model_Config
 */
class Frenet_Shipping_Model_Config
{
    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->getCarrierConfig('active');
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->getCarrierConfig('token');
    }

    /**
     * @return string
     */
    public function getWeightAttribute()
    {
        return $this->getCarrierConfig('attributes_mapping/weight_attribute');
    }

    /**
     * @return string
     */
    public function getHeightAttribute()
    {
        return $this->getCarrierConfig('attributes_mapping/height_attribute');
    }

    /**
     * @return string
     */
    public function getLengthAttribute()
    {
        return $this->getCarrierConfig('attributes_mapping/length_attribute');
    }

    /**
     * @return string
     */
    public function getWidthAttribute()
    {
        return $this->getCarrierConfig('attributes_mapping/width_attribute');
    }

    /**
     * @return float
     */
    public function getDefaultWeight()
    {
        return (float) $this->getCarrierConfig('default_measurements/default_weight');
    }

    /**
     * @return float
     */
    public function getDefaultHeight()
    {
        return (float) $this->getCarrierConfig('default_measurements/default_height');
    }

    /**
     * @return float
     */
    public function getDefaultLength()
    {
        return (float) $this->getCarrierConfig('default_measurements/default_length');
    }

    /**
     * @return float
     */
    public function getDefaultWidth()
    {
        return (float) $this->getCarrierConfig('default_measurements/default_width');
    }

    /**
     * @return int
     */
    public function getAdditionalLeadTime()
    {
        return (int) $this->getCarrierConfig('additional_lead_time');
    }

    /**
     * @return bool
     */
    public function canShowShippingForecast()
    {
        return (bool) $this->getCarrierConfig('show_shipping_forecast');
    }

    /**
     * @return bool
     */
    public function getShippingForecast()
    {
        return (string) $this->getCarrierConfig('shipping_forecast_message');
    }

    /**
     * @return bool
     */
    public function isDebugModeEnabled()
    {
        return (bool) $this->getCarrierConfig('debug');
    }

    /**
     * @return string
     */
    public function getDebugFilename()
    {
        return (string) $this->getCarrierConfig('debug_filename');
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
