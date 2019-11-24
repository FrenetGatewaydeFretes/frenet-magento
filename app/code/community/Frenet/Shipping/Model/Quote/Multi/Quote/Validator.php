<?php

class Frenet_Shipping_Model_Quote_Multi_Quote_Validator
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @inheritDoc
     */
    public function canProcessMultiQuote(Mage_Shipping_Model_Rate_Request $rateRequest)
    {
        if (!$this->objects()->config()->isMultiQuoteEnabled()) {
            return false;
        }

        $isUnlimited = $this->objects()->packageLimit()->isUnlimited();
        $isOverweight = $this->objects()->packageLimit()->isOverWeight((float) $rateRequest->getPackageWeight());

        if (!$isUnlimited && !$isOverweight) {
            return false;
        }

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($rateRequest->getAllItems() as $item) {
            /**
             * If any single product is overweight then the multi quote cannot be done.
             */
            if ($this->objects()->packageLimit()->isOverWeight((float) $item->getWeight())) {
                return false;
            }
        }

        return true;
    }
}
