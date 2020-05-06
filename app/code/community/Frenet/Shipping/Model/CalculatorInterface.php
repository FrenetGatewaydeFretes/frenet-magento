<?php

use Frenet_Shipping_Model_Packages_Package_Item as PackageItem;

/**
 * Interface Frenet_Shipping_Model_CalculatorInterface
 */
interface Frenet_Shipping_Model_CalculatorInterface
{
    /**
     * @return PackageItem[]
     */
    public function getQuote();
}
