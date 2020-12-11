<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 *
 * @author Tiago Sampaio <tiago@tiagosampaio.com>
 * @link https://github.com/tiagosampaio
 * @link https://tiagosampaio.com
 *
 * Copyright (c) 2020.
 */

/**
 * Class Frenet_Shipping_Model_Packages_Package_Factory
 */
class Frenet_Shipping_Model_Packages_Package_Factory
{
    /**
     * @return Frenet_Shipping_Model_Packages_Package
     */
    public function create()
    {
        return Mage::getModel('frenet_shipping/packages_package');
    }
}
