<?php

/**
 * Trait Frenet_Shipping_Helper_ObjectsTrait
 */
trait Frenet_Shipping_Helper_ObjectsTrait
{
    /**
     * @return Frenet_Shipping_Helper_Objects
     */
    private function objects()
    {
        return Mage::helper('frenet_shipping/objects');
    }
}
