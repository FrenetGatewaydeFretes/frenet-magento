<?php

use Mage_Sales_Model_Quote_Item as Item;
use Frenet_Shipping_Model_Packages_Package_Item as PackageItem;

class Frenet_Shipping_Model_Packages_Package
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var array
     */
    private $items = [];

    /**
     * @var Frenet_Shipping_Model_Packages_Package_Limit
     */
    private $packageLimit;

    /**
     * @var Frenet_Shipping_Model_Catalog_Product_Dimensions_ExtractorInterface
     */
    private $dimensionsExtractor;

    /**
     * @var Frenet_Shipping_Model_Packages_Package_Item_Factory
     */
    private $packageItemFactory;

    public function __construct()
    {
        $this->dimensionsExtractor = $this->objects()->productDimensionsExtractor();
        $this->packageItemFactory = $this->objects()->packageItemFactory();
        $this->packageLimit = $this->objects()->packageLimit();
    }

    /**
     * @param Item $item
     * @param int  $qty
     *
     * @return bool
     */
    public function addItem(Item $item, $qty = 1)
    {
        if (!$this->canAddItem($item, $qty)) {
            return false;
        }

        /** @var PackageItem $packageItem */
        $packageItem = $this->getItemById($item->getId()) ?: $this->packageItemFactory->create()->setCartItem($item);

        $packageItem->setQty($this->getItemQty($item) + $qty);

        $this->items[$item->getId()] = $packageItem;

        return true;
    }

    /**
     * @return PackageItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param $itemId
     *
     * @return PackageItem|null
     */
    public function getItemById($itemId)
    {
        return isset($this->items[$itemId]) ? $this->items[$itemId] : null;
    }

    /**
     * @param Item $item
     * @param int  $qty
     *
     * @return bool
     */
    public function canAddItem(Item $item, $qty = 1)
    {
        $this->dimensionsExtractor->setProductByCartItem($item);

        $weight = $this->dimensionsExtractor->getWeight();
        $itemWeight = $weight * $qty;

        if (($itemWeight + $this->getTotalWeight()) > $this->packageLimit->getMaxWeight()) {
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

        /** @var PackageItem $packageItem */
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

        /** @var PackageItem $packageItem */
        foreach ($this->getItems() as $packageItem) {
            $total += $packageItem->getTotalPrice();
        }

        return $total;
    }

    /**
     * @param Item $item
     *
     * @return bool
     */
    private function itemExists(Item $item)
    {
        return isset($this->items[$item->getId()]);
    }

    /**
     * @param Item $item
     *
     * @return float
     */
    private function getItemQty(Item $item)
    {
        if ($this->itemExists($item)) {
            return (float) $this->getItemById($item->getId())->getQty();
        }

        return 0.0000;
    }
}
