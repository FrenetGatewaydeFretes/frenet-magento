<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 *
 * @author Tiago Sampaio <tiago@tiagosampaio.com>
 * @link https://github.com/tiagosampaio
 * @link https://tiagosampaio.com
 *
 * Copyright (c) 2020.
 */

use Frenet_Shipping_Model_Packages_Package as Package;
use Frenet_Shipping_Model_Packages_Package_Item as PackageItem;
use Mage_Shipping_Model_Rate_Request as RateRequest;

/**
 * Class Frenet_Shipping_Model_Packages_Package_Processor
 */
class Frenet_Shipping_Model_Packages_Package_Processor
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Frenet_Shipping_Model_Service_Api
     */
    private $apiService;

    /**
     * @var \Frenet\Command\Shipping\QuoteInterface
     */
    private $serviceQuote;

    /**
     * @var Frenet_Shipping_Model_Quote_Item_Validator
     */
    private $quoteItemValidator;

    /**
     * @var Mage_Checkout_Model_Session
     */
    private $checkoutSession;

    /**
     * @var Frenet_Shipping_Model_Config
     */
    private $config;

    /**
     * @var Frenet_Shipping_Model_Quote_Coupon_Processor
     */
    private $quoteCouponProcessor;

    /**
     * @var Frenet_Shipping_Model_Rate_Request_Service
     */
    private $rateRequestService;

    public function __construct()
    {
        $this->apiService = $this->objects()->apiService();
        $this->checkoutSession = Mage::getSingleton('checkout/session');
        $this->quoteItemValidator = $this->objects()->quoteItemValidator();
        $this->config = $this->objects()->config();
        $this->rateRequestService = $this->objects()->rateRequestService();
        $this->quoteCouponProcessor = $this->objects()->quoteCouponProcessor();
    }

    /**
     * @param Package     $package
     * @param RateRequest $rateRequest
     *
     * @return array
     */
    public function process(Package $package)
    {
        $this->initServiceQuote();
        $this->serviceQuote->setShipmentInvoiceValue($package->getTotalPrice());

        /** @var PackageItem $packageItem */
        foreach ($package->getItems() as $packageItem) {
            if (!$this->quoteItemValidator->validate($packageItem->getCartItem())) {
                continue;
            }

            $this->addPackageItemToQuote($packageItem);
        }

        return $this->callService();
    }

    /**
     * @param PackageItem $packageItem
     *
     * @return $this
     */
    private function addPackageItemToQuote(PackageItem $packageItem)
    {
        $this->serviceQuote->addShippingItem(
            $packageItem->getSku(),
            $packageItem->getQty(),
            $packageItem->getWeight(),
            $packageItem->getLength(),
            $packageItem->getHeight(),
            $packageItem->getWidth(),
            $packageItem->getProductCategories(),
            $packageItem->isProductFragile()
        );

        return $this;
    }

    /**
     * @return array
     */
    private function callService()
    {
        /** @var \Frenet\ObjectType\Entity\Shipping\Quote $result */
        $result = $this->serviceQuote->execute();
        $services = $result->getShippingServices();

        return $services ?: [];
    }

    /**
     * @param RateRequest $rateRequest
     *
     * @return $this
     */
    private function initServiceQuote()
    {
        /** @var RateRequest $rateRequest */
        $rateRequest = $this->rateRequestService->getRateRequest();

        /** @var \Frenet\Command\Shipping\QuoteInterface $quote */
        $this->serviceQuote = $this->apiService->shipping()->quote();
        $this->serviceQuote->setSellerPostcode($this->config->getOriginPostcode())
            ->setRecipientPostcode($rateRequest->getDestPostcode())
            ->setRecipientCountry($rateRequest->getCountryId());

        $this->quoteCouponProcessor->applyCouponCode($this->serviceQuote);

        return $this;
    }
}
