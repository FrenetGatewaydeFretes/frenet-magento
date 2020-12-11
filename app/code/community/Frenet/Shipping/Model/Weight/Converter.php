<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 *
 * @author Tiago Sampaio <tiago@tiagosampaio.com>
 * @link https://github.com/tiagosampaio
 * @link https://tiagosampaio.com
 *
 * Copyright (c) 2020.
 */

/**
 * Class Frenet_Shipping_Model_Weight_Converter
 */
class Frenet_Shipping_Model_Weight_Converter implements Frenet_Shipping_Model_Weight_ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convertToKg($weight)
    {
        switch ($this->getWeightUnit()) {
            case 'lbs':
                return $weight * self::LBS_TO_KG_FACTOR;
            case 'kgs':
            default:
                return $weight;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToLbs($weight)
    {
        switch ($this->getWeightUnit()) {
            case 'kgs':
                return $weight * self::KG_TO_LBS_FACTOR;
            case 'lbs':
            default:
                return $weight;
        }
    }

    /**
     * @return string|null
     */
    private function getWeightUnit()
    {
        return Mage::getStoreConfig('general/locale/weight_unit');
    }
}
