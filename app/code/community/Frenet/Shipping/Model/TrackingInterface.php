<?php

/**
 * Interface Frenet_Shipping_Model_TrackingInterface
 */
interface Frenet_Shipping_Model_TrackingInterface
{
    /**
     * @param string $number
     * @param string $shippingServiceCode
     * @return \Frenet\ObjectType\Entity\Tracking\TrackingInfoInterface
     */
    public function track($number, $shippingServiceCode);
}
