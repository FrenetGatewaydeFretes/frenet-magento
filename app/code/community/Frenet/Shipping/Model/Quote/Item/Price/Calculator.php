<?php

use Mage_Sales_Model_Quote_Item as QuoteItem;

class Frenet_Shipping_Model_Quote_Item_Price_Calculator
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
     * @param QuoteItem $item
     *
     * @return float
     */
    public function getPrice(QuoteItem $item)
    {
        return $this->getRealItem($item)->getPrice();
    }

    /**
     * @param QuoteItem $item
     *
     * @return float
     */
    public function getFinalPrice(QuoteItem $item)
    {
        $realItem = $this->getRealItem($item);
        return $realItem->getRowTotal() / $this->itemQuantityCalculator->calculate($realItem);
    }

    /**
     * @param QuoteItem $item
     *
     * @return QuoteItem
     */
    private function getRealItem(QuoteItem $item)
    {
        $type = $item->getProductType();

        if ($item->getParentItemId()) {
            $type = $item->getParentItem()->getProductType();
        }

        switch ($type) {
            case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                return $item->getParentItem();
            case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
                /**
                 * Product is Grouped.
                 * @todo Validate if this approach is the correct one.
                 */
            case Mage_Catalog_Model_Product_Type::TYPE_BUNDLE:
                /**
                 * Product is Bundle.
                 * @todo Validate if this approach is the correct one.
                 */
            case Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL:
            case Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE:
            case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE:
            default:
                return $item;
        }
    }
}
