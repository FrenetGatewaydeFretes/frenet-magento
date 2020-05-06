<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 * @package  Frenet_Shipping
 * @author   Tiago Sampaio <tiago@tiagosampaio.com>
 * @link     https://github.com/tiagosampaio
 * @link     https://tiagosampaio.com
 *
 * Copyright (c) 2019.
 */

use Frenet_Shipping_Model_Catalog_Product_Dimensions_ExtractorInterface as ProductExtractorInterface;
use Mage_Catalog_Model_Product as Product;
use Mage_Catalog_Model_Resource_Product as ProductResource;
use Mage_Sales_Model_Quote_Item as QuoteItem;

/**
 * Class Frenet_Shipping_Model_Catalog_Product_Dimensions_Extractor
 */
class Frenet_Shipping_Model_Catalog_Product_Dimensions_Extractor implements ProductExtractorInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var QuoteItem
     */
    private $cartItem;

    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var Frenet_Shipping_Model_Catalog_Product_Attributes_MappingInterface
     */
    private $attributesMapping;

    /**
     * @var Frenet_Shipping_Model_Config
     */
    private $config;

    public function __construct()
    {
        $this->productResource = Mage::getResourceSingleton('catalog/product');
        $this->attributesMapping = $this->objects()->productAttributesMapping();
        $this->config = $this->objects()->config();
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct(Product $product)
    {
        if ($this->validateProduct($product)) {
            $this->product = $product;
        }

        return $this;
    }

    /**
     * @param QuoteItem $cartItem
     *
     * @return $this
     */
    public function setProductByCartItem(QuoteItem $cartItem)
    {
        $this->cartItem = $cartItem;
        $this->setProduct($this->cartItem->getProduct());
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        $value = $this->extractData($this->attributesMapping->getWeightAttributeCode());

        if (empty($value)) {
            $value = $this->config->getDefaultWeight();
        }

        return (float) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        $value = $this->extractData($this->attributesMapping->getHeightAttributeCode());

        if (empty($value)) {
            $value = $this->config->getDefaultHeight();
        }

        return (float) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        $value = $this->extractData($this->attributesMapping->getWidthAttributeCode());

        if (empty($value)) {
            $value = $this->config->getDefaultWidth();
        }

        return (float) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getLength()
    {
        $value = $this->extractData($this->attributesMapping->getLengthAttributeCode());

        if (empty($value)) {
            $value = $this->config->getDefaultLength();
        }

        return (float) $value;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    private function extractData($key)
    {
        if (!$this->product) {
            return null;
        }

        if ($this->cartItem->getData($key)) {
            return $this->cartItem->getData($key);
        }

        if ($this->product->getData($key)) {
            return $this->product->getData($key);
        }

        $value = $this->productResource->getAttributeRawValue(
            $this->product->getId(),
            $key,
            $this->product->getStore()
        );

        return $value;
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    private function validateProduct(Product $product)
    {
        if (!$product->getId()) {
            return false;
        }

        if (!$product->getStoreId()) {
            return false;
        }

        return true;
    }
}
