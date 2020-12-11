<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 *
 * @author   Tiago Sampaio <tiago@tiagosampaio.com>
 * @link     https://github.com/tiagosampaio
 * @link     https://tiagosampaio.com
 *
 * Copyright (c) 2020.
 */

/**
 * Class Frenet_Shipping_Model_Catalog_Product_Category_Extractor
 */
class Frenet_Shipping_Model_Catalog_Product_Category_Extractor
{
    /**
     * @var string
     */
    const CATEGORY_SEPARATOR = '|';

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string|null
     */
    public function getProductCategories(Mage_Catalog_Model_Product $product)
    {
        try {
            /** @var Mage_Catalog_Model_Resource_Category_Collection $collection */
            $collection = $product->getCategoryCollection();
            $collection->addAttributeToSelect('name');
        } catch (\Exception $e) {
            return null;
        }

        $categories = [];

        /** @var Mage_Catalog_Model_Category $category */
        foreach ($collection as $category) {
            $categories[] = $category->getName();
        }

        if (!empty($categories)) {
            return implode(self::CATEGORY_SEPARATOR, $categories);
        }

        return null;
    }
}
