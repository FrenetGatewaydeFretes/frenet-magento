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

/**
 * Interface QuoteInterface
 */
interface Frenet_Shipping_Model_Catalog_Product_View_QuoteInterface
{
    /**
     * @param int    $id
     * @param string $postcode
     * @param int    $qty
     * @param array  $options
     *
     * @return array
     */
    public function quoteByProductId($id, $postcode, $qty = 1, array $options = []);

    /**
     * @param string $sku
     * @param string $postcode
     * @param int    $qty
     * @param array  $options
     *
     * @return array
     */
    public function quoteByProductSku($sku, $postcode, $qty = 1, array $options = []);
}
