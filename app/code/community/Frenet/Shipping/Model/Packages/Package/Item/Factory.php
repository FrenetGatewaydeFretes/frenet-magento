<?php

/**
 * Class Factory
 *  */
class Frenet_Shipping_Model_Packages_Package_Item_Factory
{
    /**
     * @return Frenet_Shipping_Model_Packages_Package_Item
     */
    public function create()
    {
        return Mage::getModel('frenet_shipping/packages_package_item');
    }
}
