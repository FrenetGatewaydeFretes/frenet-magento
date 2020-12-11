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

use Mage_Checkout_Model_Session as CheckoutSession;
use Mage_Sales_Model_Quote as Quote;

/**
 * Class Frenet_Shipping_Model_Totals_Collector
 */
class Frenet_Shipping_Model_Totals_Collector
{
    /**
     * @var array
     */
    private $discounts;

    /**
     * @var array
     */
    private $additions;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    public function __construct()
    {
        $this->discounts = [];
        $this->additions = [];
        $this->checkoutSession = Mage::getSingleton('checkout/session');
    }

    /**
     * @return array
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * @return array
     */
    public function getAdditions()
    {
        return $this->additions;
    }

    /**
     * @param Quote $quote
     *
     * @return float
     */
    public function calculateQuoteDiscounts(Quote $quote = null)
    {
        return $this->iterateCollectors($this->getDiscounts(), $quote);
    }

    /**
     * @param Quote $quote
     *
     * @return float
     */
    public function calculateQuoteAdditions(Quote $quote = null)
    {
        return $this->iterateCollectors($this->getAdditions(), $quote);
    }

    /**
     * @param array      $collectors
     * @param Quote|null $quote
     *
     * @return float
     */
    private function iterateCollectors(array $collectors = [], Quote $quote = null)
    {
        $total = 0.0000;
        $quote = $this->getQuote($quote);

        /** @var Totals\CollectorInterface $collector */
        foreach ($collectors as $collector) {
            $total += (float) $collector->collect($quote);
        }
        return $total;
    }

    /**
     * @param Quote $quote
     *
     * @return Quote
     */
    private function getQuote(Quote $quote = null)
    {
        if (!$quote) {
            return $this->checkoutSession->getQuote();
        }
        return $quote;
    }
}
