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

use Mage_Sales_Model_Quote_Item as AbstractItem;
use Mage_Catalog_Model_Product as Product;

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
    public function validate(AbstractItem $item)
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
     * @param AbstractItem $item
     *
     * @return bool|Product
     */
    private function getProduct(AbstractItem $item)
    {
        /** @var Product $product */
        $product = $item->getProduct();
        return $product;
    }
}
