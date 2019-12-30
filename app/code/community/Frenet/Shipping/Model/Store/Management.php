<?php

/**
 * Class Management
 **/
class Frenet_Shipping_Model_Store_Management
{
    /**
     * @param null|string|int $id
     *
     * @return Mage_Core_Model_Store
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStore($id = null)
    {
        return Mage::app()->getStore($id);
    }
}
