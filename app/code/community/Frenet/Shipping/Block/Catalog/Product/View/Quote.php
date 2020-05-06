<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 * @package Frenet\Shipping
 *
 * @author Tiago Sampaio <tiago@tiagosampaio.com>
 * @link https://github.com/tiagosampaio
 * @link https://tiagosampaio.com
 *
 * Copyright (c) 2020.
 */

/**
 * Class Frenet_Shipping_Block_Catalog_Product_View_Quote
 */
class Frenet_Shipping_Block_Catalog_Product_View_Quote extends Mage_Core_Block_Template
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _beforeToHtml()
    {
        // $this->jsLayout['components']['frenet-quote']['config']['url'] = $this->getViewModel()->getUrl();
        parent::_beforeToHtml();
    }

    /**
     * @return string
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _toHtml()
    {
        if (!$this->isProductQuoteAllowed()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return bool
     */
    public function isProductQuoteAllowed()
    {
        if (!$this->getProduct()) {
            return false;
        }

        if (!$this->objects()->config()->isProductQuoteEnabled()) {
            return false;
        }

        $typeId = $this->getProduct()->getTypeId();
        return $this->objects()->config()->isProductQuoteAllowed($typeId);
    }
}
