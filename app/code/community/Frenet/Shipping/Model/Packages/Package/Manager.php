<?php

class Frenet_Shipping_Model_Packages_Package_Manager
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Frenet_Shipping_Model_Packages_Package
     */
    private $currentPackage = null;

    /**
     * @var Frenet_Shipping_Model_Packages_Package[]
     */
    private $packages = [];

    /**
     * @param Mage_Shipping_Model_Rate_Request $rateRequest
     *
     * @return $this
     */
    public function process(Mage_Shipping_Model_Rate_Request $rateRequest)
    {
        $this->distribute($rateRequest);
        return $this;
    }

    /**
     * @return Frenet_Shipping_Model_Packages_Package[]
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @return int
     */
    public function countPackages()
    {
        return count($this->getPackages());
    }

    /**
     * @return $this
     */
    public function unsetCurrentPackage()
    {
        $this->currentPackage = null;
        return $this;
    }

    /**
     * @param Mage_Shipping_Model_Rate_Request $rateRequest
     *
     * @return $this
     */
    private function distribute(Mage_Shipping_Model_Rate_Request $rateRequest)
    {
        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($this->getUnitItems($rateRequest) as $item) {
            $this->addItemToPackage($item);
        }

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     *
     * @return bool
     */
    private function addItemToPackage(Mage_Sales_Model_Quote_Item $item)
    {
        if (!$this->getPackage()->canAddItem($item, 1)) {
            $this->useNewPackage();
        }

        return $this->getPackage()->addItem($item, 1);
    }

    /**
     * @return Frenet_Shipping_Model_Packages_Package
     */
    private function getPackage()
    {
        if (null === $this->currentPackage) {
            $this->useNewPackage();
        }

        return $this->currentPackage;
    }

    /**
     * @return Frenet_Shipping_Model_Packages_Package
     */
    private function useNewPackage()
    {
        $this->currentPackage = $this->createPackage();

        if ($this->objects()->packageLimit()->isUnlimited()) {
            $this->packages['full'] = $this->currentPackage;
        }

        if (!$this->objects()->packageLimit()->isUnlimited()) {
            $this->packages[] = $this->currentPackage;
        }

        return $this;
    }

    /**
     * @return Frenet_Shipping_Model_Packages_Package
     */
    private function createPackage()
    {
        return $this->objects()->package();
    }

    /**
     * @param Mage_Shipping_Model_Rate_Request $rateRequest
     *
     * @return array
     */
    private function getUnitItems(Mage_Shipping_Model_Rate_Request $rateRequest)
    {
        $unitItems = [];

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($rateRequest->getAllItems() as $item) {
            if (!$this->objects()->quoteItemValidator()->validate($item)) {
                continue;
            }

            $qty = $this->objects()->quoteItemQtyCalculator()->calculate($item);

            for ($i = 1; $i <= $qty; $i++) {
                $unitItems[] = $item;
            }
        }

        return $unitItems;
    }
}
