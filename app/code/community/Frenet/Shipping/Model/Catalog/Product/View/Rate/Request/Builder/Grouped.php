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

use Frenet_Shipping_Model_Catalog_Product_View_Rate_Request_Builder_BuilderInterface as BuilderInterface;
use Mage_Catalog_Model_Product as Product;
use Varien_Object as DataObject;

/**
 * Class GroupedBuilder
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Frenet_Shipping_Model_Catalog_Product_View_Rate_Request_Builder_Grouped implements BuilderInterface
{
    /**
     * @inheritDoc
     */
    public function build(Product $product, DataObject $request, array $options = [])
    {
        if ($options && isset($options['super_group'])) {
            $request->setData('super_group', $options['super_group']);

            return;
        }

        $this->buildDefaultOptions($product, $request);
    }

    /**
     * @param Product    $product
     * @param DataObject $request
     *
     * @return void
     */
    private function buildDefaultOptions(Product $product, DataObject $request)
    {
        /** @var Mage_Catalog_Model_Product_Type_Abstract $typeInstance */
        $typeInstance = $product->getTypeInstance();

        $associatedProductsQty = [];

        /** @var Product $associatedProduct */
        foreach ($typeInstance->getAssociatedProducts($product) as $associatedProduct) {
            $associatedProductsQty[$associatedProduct->getId()] = $associatedProduct->getQty() ?: 1;
        }

        $request->setData('super_group', $associatedProductsQty);
    }
}
