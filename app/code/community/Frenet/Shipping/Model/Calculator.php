<?php

use Frenet_Shipping_Model_Catalog_Product_Attributes_MappingInterface as AttributesMapping;

class Frenet_Shipping_Model_Calculator implements Frenet_Shipping_Model_CalculatorInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @inheritdoc
     */
    public function getQuote(Mage_Shipping_Model_Rate_Request $request)
    {
        if ($result = $this->objects()->cacheManager()->load($request)) {
            return $result;
        }

        /** @var \Frenet\Command\Shipping\QuoteInterface $quote */
        $quote = $this->objects()->apiService()->shipping()->quote();
        $quote->setSellerPostcode($this->objects()->config()->getOriginPostcode())
            ->setRecipientPostcode($request->getDestPostcode())
            ->setRecipientCountry($request->getCountryId())
            ->setShipmentInvoiceValue($request->getPackageValue());

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ((array) $request->getAllItems() as $item) {
            if (!$this->objects()->quoteItemValidator()->validate($item)) {
                continue;
            }

            $this->addItemToQuote($quote, $item);
        }

        /** @var \Frenet\ObjectType\Entity\Shipping\Quote $result */
        $result = $quote->execute();
        $services = $result->getShippingServices();

        if ($services) {
            $this->objects()->cacheManager()->save($services, $request);
            return $services;
        }

        return false;
    }

    /**
     * @param \Frenet\Command\Shipping\QuoteInterface $quote
     * @param Mage_Sales_Model_Quote_Item             $item
     * @return $this
     */
    private function addItemToQuote(\Frenet\Command\Shipping\QuoteInterface $quote, Mage_Sales_Model_Quote_Item $item)
    {
        $this->objects()->productDimensionsExtrator()->setProduct($this->getProduct($item));

        /** @var Frenet_Shipping_Model_Catalog_Product_Dimensions_Extractor $dimensionsExtractor */
        $dimensionsExtractor = $this->objects()->productDimensionsExtrator();

        $quote->addShippingItem(
            $item->getSku(),
            $this->objects()->quoteItemQtyCalculator()->calculate($item),
            $this->objects()->weightConverter()->convertToKg($dimensionsExtractor->getWeight()),
            $dimensionsExtractor->getLength(),
            $dimensionsExtractor->getHeight(),
            $dimensionsExtractor->getWidth(),
            $this->getProductCategory($item),
            $this->isProductFragile($item)
        );

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     *
     * @return bool|Mage_Catalog_Model_Product
     */
    private function getProduct(Mage_Sales_Model_Quote_Item $item)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $item->getProduct();
        return $product;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     * @return bool
     */
    private function isProductFragile(Mage_Sales_Model_Quote_Item $item)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $this->getProduct($item);

        if ($product->hasData(AttributesMapping::DEFAULT_ATTRIBUTE_FRAGILE)) {
            return (bool) $product->getData(
                AttributesMapping::DEFAULT_ATTRIBUTE_FRAGILE
            );
        }

        try {
            /** @var Mage_Catalog_Model_Resource_Product $resource */
            $resource = $product->getResource();
            $value = (bool) $resource->getAttributeRawValue(
                $product->getId(),
                AttributesMapping::DEFAULT_ATTRIBUTE_FRAGILE,
                Mage::app()->getStore()
            );

            return (bool) $value;
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return false;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     * @return string|null
     */
    private function getProductCategory(Mage_Sales_Model_Quote_Item $item)
    {
        if ($item->getParentItemId()) {
            return $this->getProductCategory($item->getParentItem());
        }

        try {
            /** @var Mage_Catalog_Model_Resource_Category_Collection $collection */
            $collection = $this->getProduct($item)->getCategoryCollection();
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
            return implode('|', $categories);
        }

        return null;
    }
}
