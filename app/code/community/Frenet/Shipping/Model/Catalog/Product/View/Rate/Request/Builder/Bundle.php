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
 * Class BundleBuilder
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Frenet_Shipping_Model_Catalog_Product_View_Rate_Request_Builder_Bundle implements BuilderInterface
{
    /**
     * @inheritDoc
     */
    public function build(Product $product, DataObject $request, array $options = [])
    {
        if ($options && isset($options['bundle_option'], $options['bundle_option'])) {
            $request->setData('bundle_option', $options['bundle_option']);
            $request->setData('bundle_option_qty', $options['bundle_option_qty']);
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
        /** @var Mage_Catalog_Model_Product_Type_Abstract $typeInstance */
        $typeInstance = $product->getTypeInstance();

        $bundleOptions = [];
        $bundleOptionsQty = [];

        /** @var Mage_Bundle_Model_Resource_Option_Collection $optionsCollection */
        $optionsCollection = $typeInstance->getOptionsCollection($product);

        /** @var Mage_Bundle_Model_Option $option */
        foreach ($optionsCollection as $option) {
            /** If the option is not required then we can by pass it. */
            if (!$option->getRequired()) {
                continue;
            }

            /** @var Mage_Bundle_Model_Selection $selection */
            $selection = $option->getDefaultSelection();

            if (!$selection) {
                /** @var Mage_Bundle_Model_Resource_Selection_Collection $selections */
                $selection = $typeInstance->getSelectionsCollection(
                    $option->getId(),
                    $product
                )->getFirstItem();
            }

            if (!$selection) {
                continue;
            }

            $bundleOptions[$option->getId()] = $selection->getSelectionId();
        }

        $request->setData('bundle_option', $bundleOptions);
        $request->setData('bundle_option_qty', $bundleOptionsQty);
    }
}
