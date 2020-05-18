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
Frenet_Shipping_Model_DependencyFinder::includeDependency();

use Mage_Catalog_Model_Product as Product;
use Mage_Shipping_Model_Rate_Request as RateRequest;
use Frenet_Shipping_Model_Rate_Request_Provider as RateRequestProvider;
use Frenet_Shipping_Model_Catalog_Product_View_Rate_Request_Builder as RateRequestBuilder;
use Frenet_Shipping_Model_Calculator as Calculator;
use Frenet_Shipping_Model_Catalog_Product_View_QuoteInterface as QuoteInterface;
use Frenet\ObjectType\Entity\Shipping\Quote\ServiceInterface;

/**
 * Class Quote
 */
class Frenet_Shipping_Model_Catalog_Product_View_Quote implements QuoteInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var RateRequestProvider
     */
    private $rateRequestProvider;

    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * @var RateRequestBuilder
     */
    private $rateRequestBuilder;

    public function __construct()
    {
        $this->rateRequestProvider = $this->objects()->rateRequestProvider();
        $this->calculator = $this->objects()->calculator();
        $this->rateRequestBuilder = $this->objects()->productViewQuoteRateRequestBuilder();
    }

    /**
     * @inheritDoc
     */
    public function quoteByProductId($productId, $postcode, $qty = 1, array $options = [])
    {
        try {
            if (!$postcode) {
                Mage::throwException('Postcode cannot be empty.');
            }

            /** @var Product $product */
            $product = Mage::getModel('catalog/product')->load($productId);

            if (!$product || !$product->getId()) {
                Mage::throwException("Product ID '{$productId}' does not exist.");
            }
        } catch (Exception $exception) {
            Mage::log($exception->getMessage());

            return [];
        }

        return $this->quote($product, $postcode, $qty, $options);
    }

    /**
     * @inheritDoc
     */
    public function quoteByProductSku($sku, $postcode, $qty = 1, array $options = [])
    {
        try {
            /** @var Product $product */
            $product = Mage::getModel('catalog/product')->load($sku, 'sku');
        } catch (Exception $exception) {
            Mage::log('Product SKU %s does not exist.', $sku);

            return [];
        }

        return $this->quote($product, $postcode, $qty, $options);
    }

    /**
     * @inheritDoc
     */
    private function quote(Product $product, $postcode, $qty = 1, $options = [])
    {
        /** @var RateRequest $rateRequest */
        $rateRequest = $this->rateRequestBuilder->build($product, $postcode, $qty, $options);
        $this->rateRequestProvider->setRateRequest($rateRequest);
        $services = $this->calculator->getQuote();

        return $this->prepareResult($services);
    }

    /**
     * @param ServiceInterface[] $services
     *
     * @return array
     */
    private function prepareResult(array $services)
    {
        $result = [];

        /** @var ServiceInterface $service */
        foreach ($services as $service) {
            if (true === $service->isError()) {
                continue;
            }

            $result[] = $this->prepareService($service);
        }

        return $result;
    }

    /**
     * @param ServiceInterface $service
     *
     * @return array
     */
    private function prepareService(ServiceInterface $service)
    {
        return [
            'service_code'        => $service->getServiceCode(),
            'carrier'             => $service->getCarrier(),
            'message'             => $service->getMessage(),
            'delivery_time'       => $service->getDeliveryTime(),
            'service_description' => $service->getServiceDescription(),
            'shipping_price'      => $service->getShippingPrice(),
        ];
    }
}
