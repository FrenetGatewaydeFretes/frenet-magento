<?php

use Frenet_Shipping_Model_Catalog_Product_Attributes_MappingInterface as AttributesMapping;

/**
 * Class Frenet_Shipping_Model_Resource_Setup
 */
class Frenet_Shipping_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup
{
    /**
     * {@inheritdoc}
     */
    public function installAttributes()
    {
        $this->startSetup();
        $this->configureNewInstallation();
        $this->endSetup();
    }

    /**
     * Creates the new attributes during the module installation.
     */
    private function configureNewInstallation()
    {
        /** @var Mage_Catalog_Model_Resource_Setup $setup */
        $setup = Mage::getResourceModel('catalog/setup', 'core/resource');

        /**
         * @var string $code
         * @var array  $data
         */
        foreach ($this->getAttributes() as $code => $data) {
            $setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $code, $data);
        }
    }

    /**
     * @return array
     */
    private function getAttributes()
    {
        $attributes = array(
            AttributesMapping::DEFAULT_ATTRIBUTE_LENGTH    => array(
                'label'       => $this->__('Length (cm)'),
                'description' => $this->__("Product's package length (for shipping calculation, minimum of 16cm)."),
                'note'        => $this->__("Product's package length (for shipping calculation, minimum of 16cm)."),
                'default'     => 16,
                'type'        => 'int',
            ),
            AttributesMapping::DEFAULT_ATTRIBUTE_HEIGHT    => array(
                'label'       => $this->__('Height (cm)'),
                'description' => $this->__("Product's package height (for shipping calculation, minimum of 2cm)."),
                'note'        => $this->__("Product's package height (for shipping calculation, minimum of 2cm)."),
                'default'     => 2,
                'type'        => 'int',
            ),
            AttributesMapping::DEFAULT_ATTRIBUTE_WIDTH     => array(
                'label'       => $this->__('Width (cm)'),
                'description' => $this->__("Product's package width (for shipping calculation, minimum of 11cm)."),
                'note'        => $this->__("Product's package width (for shipping calculation, minimum of 11cm)."),
                'default'     => 11,
                'type'        => 'int',
            ),
            AttributesMapping::DEFAULT_ATTRIBUTE_LEAD_TIME => array(
                'label'       => $this->__('Lead Time (days)'),
                'description' => $this->__("Product's manufacturing time (for shipping calculation)."),
                'note'        => $this->__("Product's manufacturing time (for shipping calculation)."),
                'default'     => 0,
                'type'        => 'int',
            ),
            AttributesMapping::DEFAULT_ATTRIBUTE_FRAGILE   => array(
                'label'       => $this->__('Is Product Fragile?'),
                'description' => $this->__('Whether the product contains any fragile materials (for shipping calculation).'),
                'note'        => $this->__('Whether the product contains any fragile materials (for shipping calculation).'),
                'default'     => false,
                'type'        => 'int',
                'input'       => 'boolean',
                'backend'     => Mage_Catalog_Model_Product_Attribute_Backend_Boolean::class,
                'source'      => Mage_Catalog_Model_Product_Attribute_Source_Boolean::class,
            ),
        );

        return $attributes;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    private function __($text)
    {
        return Mage::helper('frenet_shipping')->__($text);
    }
}
