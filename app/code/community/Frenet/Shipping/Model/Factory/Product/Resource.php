<?php

/**
 * Class Resource
 **/
class Frenet_Shipping_Model_Factory_Product_Resource
{
    /**
     * @return Mage_Catalog_Model_Resource_Product
     */
    public function create()
    {
        return Mage::getResourceModel('catalog/product');
    }
}
