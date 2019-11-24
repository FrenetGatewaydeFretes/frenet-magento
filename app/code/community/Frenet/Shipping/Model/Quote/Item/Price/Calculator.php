<?php

class Frenet_Shipping_Model_Quote_Item_Price_Calculator
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     *
     * @return float
     */
    public function getPrice(Mage_Sales_Model_Quote_Item $item)
    {
        return $this->getRealItem($item)->getPrice();
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     *
     * @return float
     */
    public function getFinalPrice(Mage_Sales_Model_Quote_Item $item)
    {
        $realItem = $this->getRealItem($item);
        return $realItem->getRowTotal() / $this->objects()->quoteItemQtyCalculator()->calculate($realItem);
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    private function getRealItem(Mage_Sales_Model_Quote_Item $item)
    {
        $type = $item->getProductType();

        if ($item->getParentItemId()) {
            $type = $item->getParentItem()->getProductType();
        }

        switch ($type) {
            case Mage_Catalog_Model_Product_Type::TYPE_BUNDLE:
                // $qty = $this->calculateBundleProduct($item);
                break;

            case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
                // $qty = $this->calculateGroupedProduct($item);
                break;

            case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                return $item->getParentItem();

            case Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL:
            case Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE:
            case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE:
            default:
                return $item;
        }
    }
}
