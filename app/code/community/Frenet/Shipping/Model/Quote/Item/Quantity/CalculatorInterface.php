<?php

/**
 * Interface Frenet_Shipping_Model_Quote_Item_Quantity_CalculatorInterface
 */
interface Frenet_Shipping_Model_Quote_Item_Quantity_CalculatorInterface
{
    /**
     * @param Mage_Sales_Model_Quote_Item $item
     *
     * @return integer
     */
    public function calculate(Mage_Sales_Model_Quote_Item $item);
}
