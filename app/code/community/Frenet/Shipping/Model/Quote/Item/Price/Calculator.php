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

use Mage_Sales_Model_Quote_Item as QuoteItem;

/**
 * Class Frenet_Shipping_Model_Quote_Item_Price_Calculator
 */
class Frenet_Shipping_Model_Quote_Item_Price_Calculator
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var PriceCalculatorFactory
     */
    private $priceCalculatorFactory;

    public function __construct()
    {
        $this->priceCalculatorFactory = $this->objects()->quoteItemPriceCalculatorFactory();
    }

    /**
     * @param QuoteItem $item
     *
     * @return float
     */
    public function getPrice(QuoteItem $item)
    {
        return $this->priceCalculatorFactory->create($item)->getPrice($item);
    }

    /**
     * @param QuoteItem $item
     *
     * @return float
     */
    public function getFinalPrice(QuoteItem $item)
    {
        return $this->priceCalculatorFactory->create($item)->getFinalPrice($item);
    }
}
