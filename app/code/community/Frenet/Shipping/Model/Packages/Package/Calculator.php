<?php

use Mage_Shipping_Model_Rate_Request as RateRequest;
use Frenet_Shipping_Model_Packages_Package as Package;
use Frenet_Shipping_Model_Packages_Package_item as PackageItem;
use Frenet_Shipping_Model_Packages_Package_Manager as PackageManager;
use Frenet_Shipping_Model_Packages_Package_Matching as PackageMatching;
use Frenet_Shipping_Model_Packages_Package_Limit as PackageLimit;
use Frenet\Command\Shipping\QuoteInterface;

class Frenet_Shipping_Model_Packages_Package_Calculator
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var \Frenet\Command\Shipping\QuoteInterface
     */
    private $serviceQuote;

    /**
     * @var Mage_Shipping_Model_Rate_Request
     */
    private $rateRequest;

    /**
     * @var Frenet_Shipping_Model_Quote_Item_Validator
     */
    private $quoteItemValidator;

    /**
     * @var PackageManager
     */
    private $packageManager;

    /**
     * @var Frenet_Shipping_Model_Service_Api
     */
    private $apiService;

    /**
     * @var Frenet_Shipping_Model_Config
     */
    private $config;

    /**
     * @var Frenet_Shipping_Model_Quote_Multi_Quote_Validator
     */
    private $multiQuoteValidator;

    /**
     * @var Mage_Checkout_Model_Session
     */
    private $checkoutSession;

    /**
     * @var PackageLimit
     */
    private $packageLimit;

    /**
     * @var PackageMatching
     */
    private $packageMatching;

    public function __construct()
    {
        $this->checkoutSession = Mage::getSingleton('checkout/session');
        $this->quoteItemValidator = $this->objects()->quoteItemValidator();
        $this->packageManager = $this->objects()->packageManager();
        $this->apiService = $this->objects()->apiService();
        $this->config = $this->objects()->config();
        $this->multiQuoteValidator = $this->objects()->quoteMultiQuoteValidator();
        $this->packageLimit = $this->objects()->packageLimit();
        $this->packageMatching = $this->objects()->packageMatching();
    }

    /**
     * @param RateRequest $rateRequest
     *
     * @return RateRequest[]
     */
    public function calculate(RateRequest $rateRequest)
    {
        $this->rateRequest = $rateRequest;

        if (!$this->packageLimit->isOverWeight((float) $rateRequest->getPackageWeight())) {
            return $this->processPackages();
        }

        /**
         * If the multi quote is disabled, we remove the limit.
         */
        if (!$this->multiQuoteValidator->canProcessMultiQuote($rateRequest)) {
            $this->packageLimit->removeLimit();

            return $this->processPackages();
        }

        /**
         * Make a full call first because of the other companies that don't have weight limit like Correios.
         */
        $this->packageLimit->removeLimit();
        $this->packageManager->process($this->rateRequest);
        $this->packageManager->unsetCurrentPackage();

        /**
         * Reset the limit so the next process will split the cart into packages.
         */
        $this->packageLimit->resetMaxWeight();
        $packages = $this->processPackages();

        /**
         * Package Matching binds the results for Correios only.
         * The other options (not for Correios) are got from the full call (the first one).
         */
        return $this->packageMatching->match($packages);
    }

    /**
     * @return array
     */
    private function processPackages()
    {
        $this->packageManager->process($this->rateRequest);
        $results = [];

        /** @var Package $package */
        foreach ($this->packageManager->getPackages() as $key => $package) {
            /** @var array $services */
            $services = $this->processPackage($package);

            /**
             * If there's only one package then we can simply return the services quote.
             */
            if ($this->packageManager->countPackages() == 1) {
                return $services;
            }

            /**
             * Otherwise we need to bind the quotes.
             */
            $results[$key] = $services;
        }

        return $results;
    }

    /**
     * @param Package $package
     *
     * @return array|bool
     */
    private function processPackage(Package $package)
    {
        $this->initServiceQuote($this->rateRequest);
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
     * @return array|bool
     */
    private function callService()
    {
        /** @var \Frenet\ObjectType\Entity\Shipping\Quote $result */
        $result = $this->serviceQuote->execute();
        $services = $result->getShippingServices();

        if ($services) {
            return $services;
        }

        return false;
    }

    /**
     * @param RateRequest $rateRequest
     *
     * @return $this
     */
    private function initServiceQuote(RateRequest $rateRequest)
    {
        /** @var \Frenet\Command\Shipping\QuoteInterface $quote */
        $this->serviceQuote = $this->apiService->shipping()->quote();
        $this->serviceQuote->setSellerPostcode($this->config->getOriginPostcode())
            ->setRecipientPostcode($rateRequest->getDestPostcode())
            ->setRecipientCountry($rateRequest->getCountryId());

        /**
         * Add coupon code if exists.
         */
        if ($this->getQuoteCouponCode()) {
            $this->serviceQuote->setCouponCode($this->getQuoteCouponCode());
        }

        return $this;
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
     * @return string
     */
    private function getQuoteCouponCode()
    {
        return $this->checkoutSession->getQuote()->getCouponCode();
    }
}
