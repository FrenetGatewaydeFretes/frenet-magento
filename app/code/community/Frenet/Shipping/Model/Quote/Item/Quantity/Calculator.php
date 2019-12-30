<?php

use Mage_Sales_Model_Quote_Item as Item;

/**
 * Class Frenet_Shipping_Model_Quote_Item_Quantity_Calculator
 */
class Frenet_Shipping_Model_Quote_Item_Quantity_Calculator
    implements Frenet_Shipping_Model_Quote_Item_Quantity_CalculatorInterface
{
    /**
     * @param Item $item
     *
     * @return integer
     */
    public function calculate(Item $item)
    {
        $type = $item->getProductType();

        if ($item->getParentItemId()) {
            $type = $item->getParentItem()->getProductType();
        }

        switch ($type) {
            case Mage_Catalog_Model_Product_Type::TYPE_BUNDLE:
                $qty = $this->calculateBundleProduct($item);
                break;

            case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
                $qty = $this->calculateGroupedProduct($item);
                break;

            case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                $qty = $this->calculateConfigurableProduct($item);
                break;

            case Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL:
            case Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE:
            case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE:
            default:
                $qty = $this->calculateSimpleProduct($item);
        }

        return (int) max(1, $qty);
    }

    /**
     * @param Item $item
     *
     * @return float|int|mixed
     */
    private function calculateSimpleProduct(Item $item)
    {
        return $item->getQty();
    }

    /**
     * @param Item $item
     *
     * @return float|int|mixed
     */
    private function calculateBundleProduct(Item $item)
    {
        $bundleQty = (float) $item->getParentItem()->getQty();
        return $item->getQty() * $bundleQty;
    }

    /**
     * @param Item $item
     *
     * @return float|int|mixed
     */
    private function calculateGroupedProduct(Item $item)
    {
        return $item->getQty();
    }

    /**
     * The right quantity for configurable products are on the parent item.
     *
     * @param Item $item
     *
     * @return float|int|mixed
     */
    private function calculateConfigurableProduct(Item $item)
    {
        $qty = $item->getParentItemId() ? $item->getParentItem()->getQty() : $item->getQty();
        return $qty;
    }
}
