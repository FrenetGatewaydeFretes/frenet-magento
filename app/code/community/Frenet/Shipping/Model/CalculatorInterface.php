<?php

/**
 * Interface Frenet_Shipping_Model_CalculatorInterface
 */
interface Frenet_Shipping_Model_CalculatorInterface
{
    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return array
     */
    public function getQuote(Mage_Shipping_Model_Rate_Request $request);
}
