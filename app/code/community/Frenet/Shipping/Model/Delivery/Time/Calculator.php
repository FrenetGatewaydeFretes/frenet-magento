<?php

use Mage_Shipping_Model_Rate_Request as RateRequest;
use Frenet\ObjectType\Entity\Shipping\Quote\ServiceInterface;

class Frenet_Shipping_Model_Delivery_Time_Calculator
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Frenet_Shipping_Model_Config
     */
    private $config;

    /**
     * @var Frenet_Shipping_Model_Factory_Product_Resource
     */
    private $productResourceFactory;

    /**
     * @var Frenet_Shipping_Model_Store_Management
     */
    private $storeManagement;

    /**
     * DeliveryTimeCalculator constructor.
     */
    public function __construct()
    {
        $this->productResourceFactory = $this->objects()->productResourceFactory();
        $this->storeManagement = $this->objects()->storeManagement();
        $this->config = $this->objects()->config();
    }

    /**
     * @param Mage_Shipping_Model_Rate_Request $rateRequest
     * @param ServiceInterface                 $service
     *
     * @return int
     * @throws Mage_Core_Model_Store_Exception
     */
    public function calculate(RateRequest $rateRequest, ServiceInterface $service)
    {
        $serviceForecast = $service->getDeliveryTime();
        $maxProductForecast = 0;

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($rateRequest->getAllItems() as $item) {
            $leadTime = $this->extractProductLeadTime($item->getProduct());

            if ($maxProductForecast >= $leadTime) {
                continue;
            }

            $maxProductForecast = $leadTime;
        }

        return ($serviceForecast + $maxProductForecast + $this->config->getAdditionalLeadTime());
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return int
     * @throws Mage_Core_Model_Store_Exception
     */
    private function extractProductLeadTime(Mage_Catalog_Model_Product $product)
    {
        $leadTime = max($product->getData('lead_time'), 0);

        if (!$leadTime) {
            $leadTime = $this->productResourceFactory
                ->create()
                ->getAttributeRawValue($product->getId(), 'lead_time', $this->storeManagement->getStore());
        }

        return (int) $leadTime;
    }
}
