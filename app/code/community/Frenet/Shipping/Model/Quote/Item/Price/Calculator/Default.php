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
use Frenet_Shipping_Model_Quote_Item_Price_CalculatorInterface as PriceCalculatorInterface;

/**
 * Class Frenet_Shipping_Model_Quote_Item_Price_Calculator_Default
 */
class Frenet_Shipping_Model_Quote_Item_Price_Calculator_Default implements PriceCalculatorInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Frenet_Shipping_Model_Quote_Item_Quantity_CalculatorInterface
     */
    private $itemQtyCalculator;

    public function __construct()
    {
        $this->itemQtyCalculator = $this->objects()->quoteItemQtyCalculator();
    }

    /**
     * @inheritDoc
     */
    public function getPrice(QuoteItem $item)
    {
        return $item->getPrice();
    }

    /**
     * @inheritDoc
     */
    public function getFinalPrice(QuoteItem $item)
    {
        if (!$item->getRowTotal()) {
            $item->calcRowTotal();
        }

        /**
         * If the item price is still not calculated then fallback to product final price.
         */
        if (!$item->getRowTotal()) {
            $basePrice = $item->getProduct()->getFinalPrice($item->getQty());
            $item->setRowTotal($basePrice * $item->getQty());
        }

        return $item->getRowTotal() / $this->itemQtyCalculator->calculate($item);
    }
}
