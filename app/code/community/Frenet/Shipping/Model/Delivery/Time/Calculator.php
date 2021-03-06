<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 *
 * @author   Tiago Sampaio <tiago@tiagosampaio.com>
 * @link     https://github.com/tiagosampaio
 * @link     https://tiagosampaio.com
 *
 * Copyright (c) 2020.
 */

use Mage_Catalog_Model_Product as Product;
use Mage_Sales_Model_Quote_Item as QuoteItem;
use Frenet\ObjectType\Entity\Shipping\Quote\ServiceInterface;

/**
 * Class DeliveryTimeCalculator
 * @SuppressWarnings(PHPMD.LongVariable)
 */
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
    private $productResource;

    /**
     * @var Frenet_Shipping_Model_Store_Management
     */
    private $storeManagement;

    /**
     * @var Frenet_Shipping_Model_Rate_Request_Provider
     */
    private $rateRequestProvider;

    /**
     * DeliveryTimeCalculator constructor.
     */
    public function __construct()
    {
        $this->productResource = $this->objects()->productResourceFactory()->create();
        $this->storeManagement = $this->objects()->storeManagement();
        $this->config = $this->objects()->config();
        $this->rateRequestProvider = $this->objects()->rateRequestProvider();
    }

    /**
     * @param ServiceInterface $service
     *
     * @return int
     */
    public function calculate(ServiceInterface $service)
    {
        $rateRequest = $this->rateRequestProvider->getRateRequest();
        $serviceForecast = $service->getDeliveryTime();
        $maxProductForecast = 0;

        /** @var QuoteItem $item */
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
     * @param Product $product
     *
     * @return int
     */
    private function extractProductLeadTime(Product $product)
    {
        $leadTime = max($product->getData('lead_time'), 0);

        if (!$leadTime) {
            $leadTime = $this->productResource->getAttributeRawValue(
                $product->getId(),
                'lead_time',
                $this->storeManagement->getStore()
            );
        }

        return (int) $leadTime;
    }
}
