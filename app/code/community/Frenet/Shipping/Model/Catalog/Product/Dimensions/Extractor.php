<?php

class Frenet_Shipping_Model_Catalog_Product_Dimensions_Extractor
    implements Frenet_Shipping_Model_Catalog_Product_Dimensions_ExtractorInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Mage_Catalog_Model_Product
     */
    private $product;

    /**
     * @var Mage_Sales_Model_Quote_Item
     */
    private $cartItem;

    /**
     * {@inheritdoc}
     */
    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        if ($this->validateProduct($product)) {
            $this->product = $product;
        }

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $cartItem
     *
     * @return $this
     */
    public function setProductByCartItem(Mage_Sales_Model_Quote_Item $cartItem)
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
        $value = $this->extractData($this->objects()->productAttributesMapping()->getWeightAttributeCode());

        if (empty($value)) {
            $value = $this->objects()->config()->getDefaultWeight();
        }

        return (float) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        $value = $this->extractData($this->objects()->productAttributesMapping()->getHeightAttributeCode());

        if (empty($value)) {
            $value = $this->objects()->config()->getDefaultHeight();
        }

        return (float) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        $value = $this->extractData($this->objects()->productAttributesMapping()->getWidthAttributeCode());

        if (empty($value)) {
            $value = $this->objects()->config()->getDefaultWidth();
        }

        return (float) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getLength()
    {
        $value = $this->extractData($this->objects()->productAttributesMapping()->getLengthAttributeCode());

        if (empty($value)) {
            $value = $this->objects()->config()->getDefaultLength();
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

        if ($this->product->getData($key)) {
            return $this->product->getData($key);
        }

        /** @var string|mixed $value */
        $value = $this->product->getResource()->getAttributeRawValue(
            $this->product->getId(),
            $key,
            $this->product->getStore()
        );

        return $value;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return bool
     */
    private function validateProduct(Mage_Catalog_Model_Product $product)
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
