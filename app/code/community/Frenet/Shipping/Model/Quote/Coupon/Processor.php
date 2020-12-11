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

use Frenet\Command\Shipping\QuoteInterface;

/**
 * Class Frenet_Shipping_Model_Quote_Coupon_Processor
 */
class Frenet_Shipping_Model_Quote_Coupon_Processor
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Mage_Checkout_Model_Session
     */
    private $checkoutSession;

    /**
     * @var RateRequestProvider
     */
    private $requestProvider;

    public function __construct()
    {
        $this->checkoutSession = Mage::getSingleton('checkout/session');
        $this->requestProvider = $this->objects()->rateRequestProvider();
    }

    /**
     * @param QuoteInterface $quote
     *
     * @return $this
     */
    public function applyCouponCode(QuoteInterface $quote)
    {
        /** Add coupon code if exists. */
        if ($this->getQuoteCouponCode()) {
            $quote->setCouponCode($this->getQuoteCouponCode());
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCouponCode()
    {
        return $this->getQuoteCouponCode();
    }

    /**
     * @return string|null
     */
    private function getQuoteCouponCode()
    {
        try {
            return $this->getQuote()->getCouponCode();
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    private function getQuote()
    {
        /**
         * For some reason the quote from checkout session was creating a new quote.
         * When this occurs the message "Request Rate is not set" is displayed when placing order.
         * This is a workaround to solve the problem.
         */
        $allItems = $this->requestProvider->getRateRequest()->getAllItems();
        /** @var Mage_Sales_Model_Quote_Item_Abstract $item */
        foreach ($allItems as $item) {
            if ($item->getQuote()) {
                return $item->getQuote();
            }
        }
        return $this->checkoutSession->getQuote();
    }
}
