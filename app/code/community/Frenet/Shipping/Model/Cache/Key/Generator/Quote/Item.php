<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 * @package  Frenet_Shipping
 * @author   Tiago Sampaio <tiago@tiagosampaio.com>
 * @link     https://github.com/tiagosampaio
 * @link     https://tiagosampaio.com
 *
 * Copyright (c) 2019.
 */

class Frenet_Shipping_Model_Cache_Key_Generator_Quote_Item
    implements Frenet_Shipping_Model_Cache_Key_GeneratorInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Frenet_Shipping_Model_Rate_Request_Provider
     */
    private $requestProvider;

    /**
     * @var Frenet_Shipping_Model_SerializerInterface
     */
    private $serializer;

    /**
     * @var Frenet_Shipping_Model_Quote_Item_Validator
     */
    private $quoteItemValidator;

    /**
     * @var Frenet_Shipping_Model_Quote_Item_Quantity_CalculatorInterface
     */
    private $itemQtyCalculator;

    public function __construct()
    {
        $this->serializer = $this->objects()->serializer();
        $this->requestProvider = $this->objects()->rateRequestProvider();
        $this->quoteItemValidator = $this->objects()->quoteItemValidator();
        $this->itemQtyCalculator = $this->objects()->quoteItemQtyCalculator();
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $items = [];

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($this->requestProvider->getRateRequest()->getAllItems() as $item) {
            if (!$this->quoteItemValidator->validate($item)) {
                continue;
            }

            $productId = (int) $item->getProductId();

            if ($item->getParentItem()) {
                $productId = $item->getParentItem()->getProductId() . '-' . $productId;
            }

            $qty = (float) $this->itemQtyCalculator->calculate($item);

            $items[$productId] = $qty;
        }

        ksort($items);

        return $this->serializer->serialize($items);
    }
}
