<?php

/**
 * Interface Frenet_Shipping_Model_Service_FinderInterface
 */
interface Frenet_Shipping_Model_Service_FinderInterface
{
    /**
     * @param string $trackingNumber
     * @return \Frenet\ObjectType\Entity\Shipping\Info\ServiceInterface|null
     */
    public function findByTrackingNumber($trackingNumber);
}
