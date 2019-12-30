<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 * @package  Frenet\Shipping
 * @author   Tiago Sampaio <tiago@tiagosampaio.com>
 * @link     https://github.com/tiagosampaio
 * @link     https://tiagosampaio.com
 *
 * Copyright (c) 2019.
 */

/**
 * Class Frenet_Shipping_Model_Quote_Item_Validator
 */
class Frenet_Shipping_Model_Quote_Item_Validator
{
    /**
     * @param Mage_Sales_Model_Quote_Item $item
     *
     * @return bool
     */
    public function validate(Mage_Sales_Model_Quote_Item $item)
    {
        if ($this->getProduct($item)->isComposite()) {
            return false;
        }

        if ($this->getProduct($item)->isVirtual()) {
            return false;
        }

        return true;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     *
     * @return bool|Mage_Catalog_Model_Product
     */
    private function getProduct(Mage_Sales_Model_Quote_Item $item)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $item->getProduct();
        return $product;
    }
}
