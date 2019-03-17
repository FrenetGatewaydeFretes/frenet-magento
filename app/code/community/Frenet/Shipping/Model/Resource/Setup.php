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
                'label'       => Mage::helper('frenet_shipping')->__('Length (cm)'),
                'description' => Mage::helper('frenet_shipping')->__("Product's package length (for shipping calculation, minimum of 16cm)."),
                'note'        => Mage::helper('frenet_shipping')->__("Product's package length (for shipping calculation, minimum of 16cm)."),
                'default'     => 16,
                'type'        => 'int',
            ),
            AttributesMapping::DEFAULT_ATTRIBUTE_HEIGHT    => array(
                'label'       => Mage::helper('frenet_shipping')->__('Height (cm)'),
                'description' => Mage::helper('frenet_shipping')->__("Product's package height (for shipping calculation, minimum of 2cm)."),
                'note'        => Mage::helper('frenet_shipping')->__("Product's package height (for shipping calculation, minimum of 2cm)."),
                'default'     => 2,
                'type'        => 'int',
            ),
            AttributesMapping::DEFAULT_ATTRIBUTE_WIDTH     => array(
                'label'       => Mage::helper('frenet_shipping')->__('Width (cm)'),
                'description' => Mage::helper('frenet_shipping')->__("Product's package width (for shipping calculation, minimum of 11cm)."),
                'note'        => Mage::helper('frenet_shipping')->__("Product's package width (for shipping calculation, minimum of 11cm)."),
                'default'     => 11,
                'type'        => 'int',
            ),
            AttributesMapping::DEFAULT_ATTRIBUTE_LEAD_TIME => array(
                'label'       => Mage::helper('frenet_shipping')->__('Lead Time (days)'),
                'description' => Mage::helper('frenet_shipping')->__("Product's manufacturing time (for shipping calculation)."),
                'note'        => Mage::helper('frenet_shipping')->__("Product's manufacturing time (for shipping calculation)."),
                'default'     => 0,
                'type'        => 'int',
            ),
            AttributesMapping::DEFAULT_ATTRIBUTE_FRAGILE   => array(
                'label'       => Mage::helper('frenet_shipping')->__('Is Product Fragile?'),
                'description' => Mage::helper('frenet_shipping')->__('Whether the product contains any fragile materials (for shipping calculation).'),
                'note'        => Mage::helper('frenet_shipping')->__('Whether the product contains any fragile materials (for shipping calculation).'),
                'default'     => false,
                'type'        => 'int',
                'input'       => 'boolean',
                'backend'     => Mage_Catalog_Model_Product_Attribute_Backend_Boolean::class,
                'source'      => Mage_Catalog_Model_Product_Attribute_Source_Boolean::class,
            ),
        );

        return $attributes;
    }
}
