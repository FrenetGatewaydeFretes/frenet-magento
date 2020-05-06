<?php

use Frenet_Shipping_Model_Catalog_Product_Attributes_MappingInterface as AttributesMappingInterface;

/**
 * Class Frenet_Shipping_Model_Packages_Package_Item
 *
 * @package Frenet\Shipping\Model\Packages
 */
use Mage_Catalog_Model_Product as Product;
use Mage_Catalog_Model_Resource_Product as ProductResource;
use Mage_Sales_Model_Quote_Item as QuoteItem;

class Frenet_Shipping_Model_Packages_Package_Item
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var QuoteItem
     */
    private $cartItem;

    /**
     * @var float
     */
    private $qty;

    /**
     * @var Frenet_Shipping_Model_Store_Management
     */
    private $storeManagement;

    /**
     * @var Frenet_Shipping_Model_Factory_Product_Resource
     */
    private $productResourceFactory;

    /**
     * @var Frenet_Shipping_Model_Weight_ConverterInterface
     */
    private $weightConverter;

    /**
     * @var Frenet_Shipping_Model_Catalog_Product_Category_Extractor
     */
    private $categoryExtractor;

    /**
     * @var Frenet_Shipping_Model_Catalog_Product_Dimensions_ExtractorInterface
     */
    private $dimensionsExtractor;

    /**
     * @var Frenet_Shipping_Model_Quote_Item_Price_Calculator
     */
    private $itemPriceCalculator;

    public function __construct()
    {
        $this->storeManagement = $this->objects()->storeManagement();
        $this->productResourceFactory = $this->objects()->productResourceFactory();
        $this->weightConverter = $this->objects()->weightConverter();
        $this->categoryExtractor = $this->objects()->productCategoryExtractor();
        $this->dimensionsExtractor = $this->objects()->productDimensionsExtractor();
        $this->itemPriceCalculator = $this->objects()->quoteItemPriceCalculator();
    }

    /**
     * @return QuoteItem
     */
    public function getCartItem()
    {
        return $this->cartItem;
    }

    /**
     * @param QuoteItem $item
     *
     * @return $this
     */
    public function setCartItem(QuoteItem $item)
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
        return (float) $this->itemPriceCalculator->getPrice($this->cartItem);
    }

    /**
     * @return float
     */
    public function getFinalPrice()
    {
        $this->initProduct();

        /** @todo There will be needed a extractor here. */
        return (float) $this->itemPriceCalculator->getFinalPrice($this->cartItem);
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
    public function setQty($qty)
    {
        $this->qty = (float) $qty;
        return $this;
    }

    /**
     * @param bool $useParentItemIfAvailable
     *
     * @return bool|Product
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getProduct($useParentItemIfAvailable = false)
    {
        $this->initProduct();

        if ((true === $useParentItemIfAvailable) && $this->cartItem->getParentItem()) {
            return $this->getProduct($this->cartItem->getParentItem());
        }

        /** @var Product $product */
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
        return $this->weightConverter->convertToKg($this->dimensionsExtractor->getWeight());
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
        return $this->dimensionsExtractor->getLength();
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        $this->initProduct();
        return $this->dimensionsExtractor->getHeight();
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        $this->initProduct();
        return $this->dimensionsExtractor->getWidth();
    }

    /**
     * @return string|null
     */
    public function getProductCategories()
    {
        $this->initProduct();
        return $this->categoryExtractor->getProductCategories($this->getProduct(true));
    }

    /**
     * @return mixed
     */
    public function isProductFragile()
    {
        /** @var Product $product */
        $product = $this->getProduct();

        if ($product->hasData(AttributesMappingInterface::DEFAULT_ATTRIBUTE_FRAGILE)) {
            return (bool) $product->getData(AttributesMappingInterface::DEFAULT_ATTRIBUTE_FRAGILE);
        }

        try {
            /** @var ProductResource $resource */
            $resource = $this->productResourceFactory->create();
            $value = (bool) $resource->getAttributeRawValue(
                $product->getId(),
                AttributesMappingInterface::DEFAULT_ATTRIBUTE_FRAGILE,
                $this->storeManagement->getStore()
            );

            return (bool) $value;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return $this
     */
    private function initProduct()
    {
        $this->dimensionsExtractor->setProductByCartItem($this->cartItem);
        return $this;
    }
}
