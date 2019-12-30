<?php

use Frenet_Shipping_Model_Catalog_Product_Attributes_MappingInterface as AttributesMapping;
use Mage_Shipping_Model_Rate_Request as RateRequest;

class Frenet_Shipping_Model_Calculator implements Frenet_Shipping_Model_CalculatorInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Frenet_Shipping_Model_Cache_Manager
     */
    private $cacheManager;

    /**
     * @var Frenet_Shipping_Model_Packages_Package_Calculator
     */
    private $packagesCalculator;

    /**
     * Calculator constructor.
     */
    public function __construct()
    {
        $this->cacheManager = $this->objects()->cacheManager();
        $this->packagesCalculator = $this->objects()->packageCalculator();
    }

    /**
     * @inheritdoc
     */
    public function getQuote(RateRequest $request)
    {
        if ($result = $this->cacheManager->load($request)) {
            return $result;
        }

        /** @var RateRequest[] $packages */
        $services = $this->packagesCalculator->calculate($request);

        if ($services) {
            $this->cacheManager->save($services, $request);
            return $services;
        }

        return false;
    }
}
