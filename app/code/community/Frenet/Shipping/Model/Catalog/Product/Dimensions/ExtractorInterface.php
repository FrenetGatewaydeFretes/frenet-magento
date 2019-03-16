<?php

/**
 * Interface Frenet_Shipping_Model_Catalog_Product_Dimensions_ExtractorInterface
 */
interface Frenet_Shipping_Model_Catalog_Product_Dimensions_ExtractorInterface
{
    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return $this
     */
    public function setProduct(Mage_Catalog_Model_Product $product);
}
