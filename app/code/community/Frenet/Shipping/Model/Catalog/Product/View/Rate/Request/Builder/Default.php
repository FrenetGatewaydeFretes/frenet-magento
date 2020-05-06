<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 * @package Frenet\Shipping
 *
 * @author Tiago Sampaio <tiago@tiagosampaio.com>
 * @link https://github.com/tiagosampaio
 * @link https://tiagosampaio.com
 *
 * Copyright (c) 2020.
 */

use Frenet_Shipping_Model_Catalog_Product_View_Rate_Request_Builder_BuilderInterface as BuilderInterface;
use Mage_Catalog_Model_Product as Product;
use Varien_Object as DataObject;

/**
 * Class DefaultBuilder
 */
class Frenet_Shipping_Model_Catalog_Product_View_Rate_Request_Builder_Default implements BuilderInterface
{
    /**
     * @inheritDoc
     * @codingStandardsIgnoreStart
     */
    public function build(Product $product, DataObject $request, array $options = [])
    {
        //@codingStandardsIgnoreEnd
    }
}
