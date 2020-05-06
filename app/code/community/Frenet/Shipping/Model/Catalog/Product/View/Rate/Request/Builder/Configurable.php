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
 * Class ConfigurableBuilder
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Frenet_Shipping_Model_Catalog_Product_View_Rate_Request_Builder_Configurable implements BuilderInterface
{
    /**
     * @inheritDoc
     */
    public function build(Product $product, DataObject $request, array $options = [])
    {
        if ($options && isset($options['super_attribute'])) {
            $request->setData('super_attribute', $options['super_attribute']);
            return;
        }

        $this->buildDefaultOptions($product, $request);
    }

    /**
     * @param Product $product
     * @param DataObject       $request
     *
     * @return void
     */
    private function buildDefaultOptions(Product $product, DataObject $request)
    {
        $options = [];

        /** @var Mage_Catalog_Model_Product_Type_Abstract $typeInstance */
        $typeInstance = $product->getTypeInstance();
        $configurableOptions = $typeInstance->getConfigurableOptions($product);

        /**
         * Get the default attribute options.
         */
        foreach ($configurableOptions as $configurableOptionId => $configurableOption) {
            /** @var array $option */
            $option = array_shift($configurableOption);
            $options[$configurableOptionId] = $option['value_index'];
        }

        $request->setData('super_attribute', $options);
    }
}
