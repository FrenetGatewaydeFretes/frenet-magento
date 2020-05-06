<?php

use Frenet\ObjectType\Entity\Shipping\Quote\Service;

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
     * @var Frenet_Shipping_Model_Rate_Request_Provider
     */
    private $rateRequestProvider;

    /**
     * Calculator constructor.
     */
    public function __construct()
    {
        $this->cacheManager = $this->objects()->cacheManager();
        $this->packagesCalculator = $this->objects()->packageCalculator();
        $this->rateRequestProvider = $this->objects()->rateRequestProvider();
    }

    /**
     * @inheritdoc
     */
    public function getQuote()
    {
        $result = $this->cacheManager->load();
        if ($result) {
            return $result;
        }

        /** @var Service[] $services */
        $services = $this->packagesCalculator->calculate();

        foreach ($services as $service) {
            $this->processService($service);
        }

        if ($services) {
            $this->cacheManager->save($services);
            return $services;
        }

        return [];
    }

    /**
     * @param Service $service
     *
     * @return Service
     */
    private function processService(Service $service)
    {
        $service->setData(
            'service_description',
            str_replace('|', "\n", $service->getServiceDescription())
        );
        return $service;
    }
}
