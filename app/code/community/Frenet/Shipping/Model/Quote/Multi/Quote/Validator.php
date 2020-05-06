<?php

use Mage_Shipping_Model_Rate_Request as RateRequest;

/**
 * Class MultiQuoteValidator
 */
class Frenet_Shipping_Model_Quote_Multi_Quote_Validator
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Frenet_Shipping_Model_Config
     */
    private $config;

    /**
     * @var Frenet_Shipping_Model_Packages_Package_Limit
     */
    private $packageLimit;

    /**
     * @var Frenet_Shipping_Model_Rate_Request_Provider
     */
    private $rateRequestProvider;

    public function __construct()
    {
        $this->config = $this->objects()->config();
        $this->packageLimit = $this->objects()->packageLimit();
        $this->rateRequestProvider = $this->objects()->rateRequestProvider();
    }

    /**
     * @inheritDoc
     */
    public function canProcessMultiQuote()
    {
        /** @var RateRequest $rateRequest */
        $rateRequest = $this->rateRequestProvider->getRateRequest();

        if (!$this->config->isMultiQuoteEnabled()) {
            return false;
        }

        $isUnlimited = $this->packageLimit->isUnlimited();
        $isOverweight = $this->packageLimit->isOverWeight((float) $rateRequest->getPackageWeight());

        if (!$isUnlimited && !$isOverweight) {
            return false;
        }

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($rateRequest->getAllItems() as $item) {
            /**
             * If any single product is overweight then the multi quote cannot be done.
             */
            if ($this->packageLimit->isOverWeight((float) $item->getWeight())) {
                return false;
            }
        }

        return true;
    }
}
