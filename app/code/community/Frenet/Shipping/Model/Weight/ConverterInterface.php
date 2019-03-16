<?php

/**
 * Interface Frenet_Shipping_Model_Weight_ConverterInterface
 */
interface Frenet_Shipping_Model_Weight_ConverterInterface
{
    /**
     * @var float
     */
    const LBS_TO_KG_FACTOR = 0.453592;

    /**
     * @var float
     */
    const KG_TO_LBS_FACTOR = 2.20462;

    /**
     * @param float $weight
     * @return float
     */
    public function convertToKg($weight);

    /**
     * @param float $weight
     * @return float
     */
    public function convertToLbs($weight);
}
