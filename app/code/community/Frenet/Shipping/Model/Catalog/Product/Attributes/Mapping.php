<?php

class Frenet_Shipping_Model_Catalog_Product_Attributes_Mapping
    implements Frenet_Shipping_Model_Catalog_Product_Attributes_MappingInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * {@inheritdoc}
     */
    public function getWeightAttributeCode()
    {
        return $this->objects()->config()->getWeightAttribute() ?: self::DEFAULT_ATTRIBUTE_WEIGHT;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeightAttributeCode()
    {
        return $this->objects()->config()->getHeightAttribute() ?: self::DEFAULT_ATTRIBUTE_HEIGHT;
    }

    /**
     * {@inheritdoc}
     */
    public function getLengthAttributeCode()
    {
        return $this->objects()->config()->getLengthAttribute() ?: self::DEFAULT_ATTRIBUTE_LENGTH;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidthAttributeCode()
    {
        return $this->objects()->config()->getWidthAttribute() ?: self::DEFAULT_ATTRIBUTE_WIDTH;
    }

    /**
     * {@inheritdoc}
     */
    public function getLeadTimeAttributeCode()
    {
        return self::DEFAULT_ATTRIBUTE_LEAD_TIME;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragileAttributeCode()
    {
        return self::DEFAULT_ATTRIBUTE_FRAGILE;
    }
}
