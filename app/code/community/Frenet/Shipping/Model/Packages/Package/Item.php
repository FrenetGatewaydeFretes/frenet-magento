<?php

use Frenet\Shipping\Api\Data\AttributesMappingInterface;

/**
 * Class Frenet_Shipping_Model_Packages_Package_Item
 *
 * @package Frenet\Shipping\Model\Packages
 */
class Frenet_Shipping_Model_Packages_Package_Item
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Mage_Sales_Model_Quote_Item
     */
    private $cartItem;

    /**
     * @var float
     */
    private $qty;

    /**
     * @var bool
     */
    private $isInitialized = false;

    /**
     * @var Mage_Sales_Model_Quote_Item
     */
    private $storeManagement;

    /**
     * @var Mage_Catalog_Model_Resource_ProductFactory
     */
    private $productResourceFactory;

    /**
     * @var \Frenet\$this->objects()->Shipping()\Api\WeightConverterInterface
     */
    private $weightConverter;

    /**
     * @var \Frenet\Shipping\Model\Catalog\Product\CategoryExtractor
     */
    private $categoryExtractor;

    /**
     * @var \Frenet\Shipping\Api\Data\DimensionsExtractorInterface
     */
    private $dimensionsExtractor;

    /**
     * @var Frenet\Shipping\Model\Quote\ItemPriceCalculator
     */
    private $itemPriceCalculator;

    /**
     * @return Mage_Sales_Model_Quote_Item
     */
    public function getCartItem()
    {
        return $this->cartItem;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     *
     * @return $this
     */
    public function setCartItem(Mage_Sales_Model_Quote_Item $item)
    {
        $this->cartItem = $item;
        return $this;
    }

    /**
     * @return float
     */
    public function getQty()
    {
        return (float) $this->qty ?: 1;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        $this->initProduct();

        /** @todo There will be needed a extractor here. */
        return (float) $this->objects()->quoteItemPriceCalculator()->getPrice($this->cartItem);
    }

    /**
     * @return float
     */
    public function getFinalPrice()
    {
        $this->initProduct();

        /** @todo There will be needed a extractor here. */
        return (float) $this->objects()->quoteItemPriceCalculator()->getFinalPrice($this->cartItem);
    }

    /**
     * @return float
     */
    public function getTotalPrice()
    {
        $this->initProduct();
        return (float) $this->getFinalPrice() * $this->getQty();
    }

    /**
     * @param float $qty
     *
     * @return $this
     */
    public function setQty(float $qty)
    {
        $this->qty = $qty;
        return $this;
    }

    /**
     * @param bool $useParentItemIfAvailable
     *
     * @return bool|Mage_Catalog_Model_Product
     */
    public function getProduct($useParentItemIfAvailable = false)
    {
        $this->initProduct();

        if ((true === $useParentItemIfAvailable) && $this->cartItem->getParentItem()) {
            return $this->getProduct($this->cartItem->getParentItem());
        }

        /** @var Mage_Catalog_Model_Product $product */
        return $this->cartItem->getProduct();
    }

    /**
     * @return string|null
     */
    public function getSku()
    {
        return $this->cartItem->getSku();
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        $this->initProduct();
        return $this->objects()->weightConverter()->convertToKg($this->dimensionsExtractor->getWeight());
    }

    /**
     * @return float
     */
    public function getTotalWeight()
    {
        $this->initProduct();
        return (float) ($this->getWeight() * $this->getQty());
    }

    /**
     * @return float
     */
    public function getLength()
    {
        $this->initProduct();
        return $this->objects()->productDimensionsExtractor()->getLength();
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        $this->initProduct();
        return $this->objects()->productDimensionsExtractor()->getHeight();
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        $this->initProduct();
        return $this->objects()->productDimensionsExtractor()->getWidth();
    }

    /**
     * @return string|null
     */
    public function getProductCategories()
    {
        $this->initProduct();
        return $this->objects()->productCategoryExtractor()->getProductCategories($this->getProduct(true));
    }

    /**
     * @return mixed
     */
    public function isProductFragile()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $this->getProduct();

        if ($product->hasData(AttributesMappingInterface::DEFAULT_ATTRIBUTE_FRAGILE)) {
            return (bool) $product->getData(AttributesMappingInterface::DEFAULT_ATTRIBUTE_FRAGILE);
        }

        try {
            /** @var Mage_Catalog_Model_Resource_Product $resource */
            $resource = Mage::getResourceModel('catalog/product');
            $value = (bool) $resource->getAttributeRawValue(
                $product->getId(),
                AttributesMappingInterface::DEFAULT_ATTRIBUTE_FRAGILE,
                Mage::app()->getStore()
            );

            return (bool) $value;
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * @return $this
     */
    private function initProduct()
    {
        $this->objects()->productDimensionsExtractor()->setProductByCartItem($this->cartItem);
        return $this;
    }
}
