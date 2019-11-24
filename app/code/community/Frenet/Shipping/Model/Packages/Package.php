<?php

/**
 * Class Frenet_Shipping_Model_Packages_Package
 *
 * @package Frenet\Shipping\Model\Packages
 */
class Frenet_Shipping_Model_Packages_Package
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var array
     */
    private $items = [];

    /**
     * @param Item $item
     * @param int  $qty
     *
     * @return bool
     */
    public function addItem(Mage_Sales_Model_Quote_Item $item, $qty = 1)
    {
        if (!$this->canAddItem($item, $qty)) {
            return false;
        }

        /** @var Frenet_Shipping_Model_Packages_Package_Item $packageItem */
        $packageItem = $this->getItemById($item->getId()) ?: $this->objects()->packageItem()->setCartItem($item);

        $packageItem->setQty($this->getItemQty($item) + $qty);

        $this->items[$item->getId()] = $packageItem;

        return true;
    }

    /**
     * @return Frenet_Shipping_Model_Packages_Package_Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param $itemId
     *
     * @return Frenet_Shipping_Model_Packages_Package_Item|null
     */
    public function getItemById($itemId)
    {
        return isset($this->items[$itemId]) ? $this->items[$itemId] : null;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     * @param int                         $qty
     *
     * @return bool
     */
    public function canAddItem(Mage_Sales_Model_Quote_Item $item, $qty = 1)
    {
        $this->objects()->productDimensionsExtractor()->setProductByCartItem($item);

        $weight = $this->objects()->productDimensionsExtractor()->getWeight();
        $itemWeight = $weight * $qty;

        if (($itemWeight + $this->getTotalWeight()) > $this->objects()->packageLimit()->getMaxWeight()) {
            return false;
        }

        return true;
    }

    /**
     * @return float
     */
    public function getTotalWeight()
    {
        $total = 0.0000;

        /** @var Frenet_Shipping_Model_Packages_Package_Item $packageItem */
        foreach ($this->getItems() as $packageItem) {
            $total += $packageItem->getTotalWeight();
        }

        return (float) $total;
    }

    /**
     * @return float
     */
    public function getTotalPrice()
    {
        $total = 0.0000;

        /** @var Frenet_Shipping_Model_Packages_Package_Item $packageItem */
        foreach ($this->getItems() as $packageItem) {
            $total += $packageItem->getTotalPrice();
        }

        return $total;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     *
     * @return bool
     */
    private function itemExists(Mage_Sales_Model_Quote_Item $item)
    {
        return isset($this->items[$item->getId()]);
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     *
     * @return float
     */
    private function getItemQty(Mage_Sales_Model_Quote_Item $item)
    {
        if ($this->itemExists($item)) {
            return (float) $this->getItemById($item->getId())->getQty();
        }

        return 0.0000;
    }
}
