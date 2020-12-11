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
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Frenet_Shipping_Model_Quote_Item_Price_Calculator_Configurable implements PriceCalculatorInterface
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
        $parentItem = $item->getParentItem();
        return $parentItem->getPrice();
    }

    /**
     * @inheritDoc
     */
    public function getFinalPrice(QuoteItem $item)
    {
        $parentItem = $item->getParentItem();

        if (!$parentItem->getRowTotal()) {
            $parentItem->calcRowTotal();
        }

        return $parentItem->getRowTotal() / $this->itemQtyCalculator->calculate($item);
    }
}
