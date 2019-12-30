<?php

use Mage_Shipping_Model_Rate_Request as RateRequest;
use Frenet_Shipping_Model_Packages_Package as Package;

class Frenet_Shipping_Model_Packages_Package_Manager
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Package
     */
    private $currentPackage = null;

    /**
     * @var Package[]
     */
    private $packages = [];

    /**
     * @var Frenet_Shipping_Model_Packages_Package_Factory
     */
    private $packageFactory;

    /**
     * @var Frenet_Shipping_Model_Quote_Item_Validator
     */
    private $quoteItemValidator;

    /**
     * @var Frenet_Shipping_Model_Quote_Item_Quantity_CalculatorInterface
     */
    private $itemQuantityCalculator;

    /**
     * @var Frenet_Shipping_Model_Packages_Package_Limit
     */
    private $packageLimit;

    /**
     * PackageManager constructor.
     */
    public function __construct()
    {
        $this->quoteItemValidator = $this->objects()->quoteItemValidator();
        $this->itemQuantityCalculator = $this->objects()->quoteItemQtyCalculator();
        $this->packageFactory = $this->objects()->packageFactory();
        $this->packageLimit = $this->objects()->packageLimit();
    }

    /**
     * @param RateRequest $rateRequest
     *
     * @return $this
     */
    public function process(RateRequest $rateRequest)
    {
        $this->distribute($rateRequest);
        return $this;
    }

    /**
     * @return Package[]
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
     * @param RateRequest $rateRequest
     *
     * @return $this
     */
    private function distribute(RateRequest $rateRequest)
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
     * @return Package
     */
    private function getPackage()
    {
        if (null === $this->currentPackage) {
            $this->useNewPackage();
        }

        return $this->currentPackage;
    }

    /**
     * @return $this
     */
    private function useNewPackage()
    {
        $this->currentPackage = $this->createPackage();

        if ($this->packageLimit->isUnlimited()) {
            $this->packages['full'] = $this->currentPackage;
        }

        if (!$this->packageLimit->isUnlimited()) {
            $this->packages[] = $this->currentPackage;
        }

        return $this;
    }

    /**
     * @return Package
     */
    private function createPackage()
    {
        return $this->packageFactory->create();
    }

    /**
     * @param RateRequest $rateRequest
     *
     * @return array
     */
    private function getUnitItems(RateRequest $rateRequest)
    {
        $unitItems = [];

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($rateRequest->getAllItems() as $item) {
            if (!$this->quoteItemValidator->validate($item)) {
                continue;
            }

            $qty = $this->itemQuantityCalculator->calculate($item);

            for ($i = 1; $i <= $qty; $i++) {
                $unitItems[] = $item;
            }
        }

        return $unitItems;
    }
}
