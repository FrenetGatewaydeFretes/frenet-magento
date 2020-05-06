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

use Varien_Object as DataObject;
use Mage_Catalog_Model_Product as Product;
use Mage_Sales_Model_Quote_Item as QuoteItem;
use Mage_Shipping_Model_Rate_Request as RateRequest;
use Frenet\Shipping\Model\Catalog\Product\DimensionsExtractorInterface;

/**
 * Class Frenet_Shipping_Model_Catalog_Product_View_Rate_Request_Builder
 */
class Frenet_Shipping_Model_Catalog_Product_View_Rate_Request_Builder
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var array
     */
    private $builders;

    /**
     * @var DimensionsExtractorInterface
     */
    private $dimensionsExtractor;

    public function __construct()
    {
        $this->dimensionsExtractor = $this->objects()->productDimensionsExtractor();

        $default = Mage::getSingleton('frenet_shipping/catalog_product_view_rate_request_builder_default');
        $configurable = Mage::getSingleton('frenet_shipping/catalog_product_view_rate_request_builder_configurable');
        $bundle = Mage::getSingleton('frenet_shipping/catalog_product_view_rate_request_builder_bundle');
        $grouped = Mage::getSingleton('frenet_shipping/catalog_product_view_rate_request_builder_grouped');

        $this->builders = [
            'default'      => $default,
            'simple'       => $default,
            'virtual'      => $default,
            'downloadable' => $default,
            'grouped'      => $grouped,
            'configurable' => $configurable,
            'bundle'       => $bundle,
        ];
    }

    /**
     * @param Product $product
     * @param string  $postcode
     * @param int     $qty
     * @param array   $options
     *
     * @return RateRequest
     */
    public function build(Product $product, $postcode, $qty = 1, array $options = [])
    {
        $quote = $this->createQuote();
        $quote->getShippingAddress()->setPostcode($postcode);

        $request = $this->prepareProductRequest($product, $qty, $options);
        $quote->addProduct($product, $request);
        $this->fixQuoteItems($quote);

        /** @var RateRequest $rateRequest */
        $rateRequest = new RateRequest();

        $rateRequest->setAllItems($quote->getAllItems());
        $rateRequest->setDestPostcode($postcode);
        $rateRequest->setDestCountryId('BR');

        $totalWeight = 0;

        /** @var QuoteItem $item */
        foreach ($quote->getAllItems() as $item) {
            $totalWeight += $item->getRowWeight();
        }

        $rateRequest->setPackageWeight($totalWeight);

        return $rateRequest;
    }

    private function fixQuoteItems(Mage_Sales_Model_Quote $quote)
    {
        /** @var QuoteItem $item */
        foreach ($quote->getAllItems() as $item) {
            if (!$item->getId()) {
                $item->setId($item->getProduct()->getId());
            }

            $qty = $item->getProduct()->getCartQty();
            $item->setRowWeight($this->getItemRowWeight($item, $qty));
        }
    }

    /**
     * @param float $itemWeight
     * @param float $qty
     *
     * @return float
     */
    private function getItemRowWeight(QuoteItem $item, $qty) : float
    {
        $this->dimensionsExtractor->setProductByCartItem($item);
        $weight = $this->dimensionsExtractor->getWeight();

        return $weight * $qty;
    }

    /**
     * @param Product $product
     * @param int     $qty
     * @param array   $options
     *
     * @return DataObject
     */
    private function prepareProductRequest(Product $product, $qty = 1, array $options = [])
    {
        /** @var DataObject $request */
        $request = new DataObject();
        $request->setData(['qty' => $qty]);

        $this->getBuilder($product->getTypeId())->build($product, $request, $options);

        return $request;
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    private function createQuote()
    {
        return Mage::getModel('sales/quote');
    }

    /**
     * @param string $type
     *
     * @return Frenet_Shipping_Model_Catalog_Product_View_Rate_Request_Builder_BuilderInterface
     */
    private function getBuilder($type)
    {
        if (isset($this->builders[$type])) {
            return $this->builders[$type];
        }

        return $this->builders['default'];
    }
}
