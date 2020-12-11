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

use Mage_Sales_Model_Quote_Item as QuoteItem;
use Frenet_Shipping_Model_Quote_Item_Price_CalculatorInterface as PriceCalculatorInterface;

/**
 * Class Frenet_Shipping_Model_Quote_Item_Price_Calculator_Factory
 */
class Frenet_Shipping_Model_Quote_Item_Price_Calculator_Factory
{
    /**
     * @var string
     */
    const DEFAULT_CALCULATOR_TYPE = 'default';

    /**
     * @var array
     */
    private $calculators = [];

    public function __construct()
    {
        $default = Mage::getSingleton('frenet_shipping/quote_item_price_calculator_default');
        $configurable = Mage::getSingleton('frenet_shipping/quote_item_price_calculator_configurable');
        $bundle = Mage::getSingleton('frenet_shipping/quote_item_price_calculator_bundle');

        $this->calculators = [
            self::DEFAULT_CALCULATOR_TYPE => $default,
            'simple'                      => $default,
            'virtual'                     => $default,
            'downloadable'                => $default,
            'grouped'                     => $default,
            'configurable'                => $configurable,
            'bundle'                      => $bundle,
        ];
    }

    /**
     * @param QuoteItem $item
     *
     * @return PriceCalculatorInterface
     */
    public function create(QuoteItem $item)
    {
        return $this->getCalculatorInstance($item);
    }

    /**
     * @param QuoteItem $item
     *
     * @return mixed
     */
    private function getCalculatorInstance(QuoteItem $item)
    {
        $type = $this->getCalculatorType($item);
        if (isset($this->calculators[$type])) {
            return $this->calculators[$type];
        }
        return $this->calculators[$this->getDefaultCalculatorType()];
    }

    /**
     * @param QuoteItem $item
     *
     * @return string
     */
    private function getCalculatorType(QuoteItem $item)
    {
        $type = $item->getProductType();
        if ($item->getParentItemId() && $item->getParentItem()) {
            $type = $item->getParentItem()->getProductType();
        }
        return $type;
    }

    /**
     * @return string
     */
    private function getDefaultCalculatorType()
    {
        return self::DEFAULT_CALCULATOR_TYPE;
    }
}
