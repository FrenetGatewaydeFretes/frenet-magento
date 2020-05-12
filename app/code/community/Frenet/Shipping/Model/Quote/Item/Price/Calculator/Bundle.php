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
use Frenet_Shipping_Model_Quote_Item_Price_CalculatorInterface as PriceCalculatorInterface;
use Mage_Bundle_Model_Product_Price as Price;
use Mage_Sales_Model_Quote_Item as QuoteItem;

/**
 * Class DefaultPriceCalculator
 *  */
class Frenet_Shipping_Model_Quote_Item_Price_Calculator_Bundle implements PriceCalculatorInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Frenet_Shipping_Model_Quote_Item_Quantity_CalculatorInterface
     */
    private $itemQuantityCalculator;

    public function __construct()
    {
        $this->itemQuantityCalculator = $this->objects()->quoteItemQtyCalculator();
    }

    /**
     * @inheritDoc
     */
    public function getPrice(QuoteItem $item)
    {
        if ($this->isPriceTypeFixed($item)) {
            return $this->calculatePartialValue($item);
        }

        return $item->getPrice();
    }

    /**
     * @inheritDoc
     */
    public function getFinalPrice(QuoteItem $item)
    {
        if ($this->isPriceTypeFixed($item)) {
            return $this->calculatePartialValue($item);
        }

        return $item->getRowTotal() / $this->itemQuantityCalculator->calculate($item);
    }

    /**
     * This is an alternative solution for when the bundle has the Price Type Fixed.
     *
     * @param QuoteItem $item
     *
     * @return float
     */
    private function calculatePartialValue(QuoteItem $item)
    {
        /** @var QuoteItem $bundle */
        $bundle = $item->getParentItem();
        $rowTotal = (float) $bundle->getRowTotal() / $this->itemQuantityCalculator->calculate($item);

        return (float) ($rowTotal / count($bundle->getChildren()));
    }

    /**
     * @param QuoteItem $item
     *
     * @return bool
     */
    private function isPriceTypeFixed(QuoteItem $item)
    {
        /** @var QuoteItem $bundle */
        $bundle = $this->getBundleItem($item);

        if (Price::PRICE_TYPE_FIXED == $bundle->getProduct()->getPriceType()) {
            return true;
        }

        return false;
    }

    /**
     * @param QuoteItem $item
     *
     * @return QuoteItem
     */
    private function getBundleItem(QuoteItem $item)
    {
        return $item->getParentItem();
    }
}
