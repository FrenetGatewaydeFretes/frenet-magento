<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 * @package  Frenet\Shipping
 *
 * @author   Tiago Sampaio <tiago@tiagosampaio.com>
 * @link     https://github.com/tiagosampaio
 * @link     https://tiagosampaio.com
 *
 * Copyright (c) 2020.
 */

use Mage_Catalog_Model_Product as Product;
use Varien_Object as DataObject;

/**
 * Interface Frenet_Shipping_Model_Catalog_Product_View_Rate_Request_Builder_BuilderInterface
 */
interface Frenet_Shipping_Model_Catalog_Product_View_Rate_Request_Builder_BuilderInterface
{
    /**
     * @param Product    $product
     * @param DataObject $request
     * @param array      $options
     *
     * @return void
     */
    public function build(Product $product, DataObject $request, array $options = []);
}
