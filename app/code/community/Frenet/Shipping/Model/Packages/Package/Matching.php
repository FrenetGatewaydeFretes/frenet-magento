<?php

use Frenet\ObjectType\Entity\Shipping\Quote\Service;

class Frenet_Shipping_Model_Packages_Package_Matching
{
    /**
     * @var array
     */
    private $results = [];

    /**
     * @var array
     */
    private $fullResults;

    /**
     * @var array
     */
    private $services = [];

    /**
     * @param array $results
     *
     * @return array
     */
    public function match(array $results)
    {
        $this->init($results);
        return $this->matchResults();
    }

    /**
     * @return array
     */
    private function matchResults()
    {
        /** @var array $result */
        foreach ($this->results as $resultIndex => $services) {
            $this->prepareServices($services);
        }

        return $this->buildServicesResult();
    }

    /**
     * @param array $services
     *
     * @return $this
     */
    private function prepareServices(array $services)
    {
        /** @var Service $service */
        foreach ($services as $serviceIndex => $service) {
            if ($service->getCarrier() != 'Correios') {
                continue;
            }

            if ($service->isError()) {
                continue;
            }

            $this->appendService($service);
        }

        return $this;
    }

    /**
     * @param Service $service
     *
     * @return $this
     *
     * @todo Refactor this method to make it more consistent and maintainable.
     */
    private function appendService(Service $service)
    {
        $serviceCode = $service->getServiceCode();

        $object = isset($this->services[$serviceCode]) ? $this->services[$serviceCode] : $this->getNewEmpty();

        $serviceDescription = $service->getServiceDescription();
        $carrier = $service->getCarrier();
        $deliveryTime = $object['delivery_time'];
        $originalDeliveryTime = $object['original_delivery_time'];
        $originalShippingPrice = $object['original_shipping_price'] += $service->getOriginalShippingPrice();
        $responseTime = $service->getResponseTime();
        $shippingPrice = $object['shipping_price'] += $service->getShippingPrice();

        if ($service->getDeliveryTime() > $deliveryTime) {
            $deliveryTime = $service->getDeliveryTime();
        }

        if ($service->getOriginalDeliveryTime() > $originalDeliveryTime) {
            $deliveryTime = $service->getDeliveryTime();
        }

        $object = [
            'carrier'                 => $carrier,
            'delivery_time'           => $deliveryTime,
            'error'                   => false,
            'original_delivery_time'  => $originalDeliveryTime,
            'original_shipping_price' => $originalShippingPrice,
            'response_time'           => $responseTime,
            'service_code'            => $serviceCode,
            'service_description'     => $serviceDescription,
            'shipping_price'          => $shippingPrice,
        ];

        $this->services[$serviceCode] = $object;

        return $this;
    }

    /**
     * @return array
     */
    private function buildServicesResult()
    {
        $results = [];
        $factory = new \Frenet\ObjectType\Entity\Shipping\Quote\ServiceFactory();

        /** @var array $serviceData */
        foreach ($this->services as $serviceData) {
            $results[] = $factory->create()->setData($serviceData);
        }

        return array_merge($results, $this->fullResults);
    }

    /**
     * @return array
     */
    private function getNewEmpty()
    {
        return [
            'carrier'                 => null,
            'delivery_time'           => 0,
            'error'                   => false,
            'original_delivery_time'  => 0,
            'original_shipping_price' => 0.0000,
            'response_time'           => 0.0000,
            'service_code'            => null,
            'service_description'     => null,
            'shipping_price'          => 0.0000,
        ];
    }

    /**
     * @param array $results
     */
    private function init(array $results)
    {
        $this->fullResults = $results['full'];
        unset($results['full']);
        $this->results = $results;
        $this->processFullResults();

        return $this;
    }

    /**
     * @return $this
     */
    private function processFullResults()
    {
        /** @var Service $service */
        foreach ($this->fullResults as $index => $service) {
            if (true === $service->isError()) {
                unset($this->fullResults[$index]);
            }
        }

        return $this;
    }
}
