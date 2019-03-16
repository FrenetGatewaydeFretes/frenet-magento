<?php

/**
 * Class Frenet_Shipping_Model_Service_Finder
 */
class Frenet_Shipping_Model_Service_Finder implements Frenet_Shipping_Model_Service_FinderInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @inheritdoc
     */
    public function findByTrackingNumber($trackingNumber)
    {
        $names = $this->getShipmentPossibleNames($trackingNumber);

        if (empty($names)) {
            return null;
        }

        /** @var \Frenet\ObjectType\Entity\Shipping\InfoInterface $info */
        $info = $this->objects()->apiService()->shipping()->info()->execute();
        $services = (array) $info->getAvailableShippingServices();

        /** @var string $name */
        foreach ($names as $name) {
            if ($service = $this->machServiceByName($services, $name)) {
                return $service;
            }
        }

        return null;
    }

    /**
     * @param \Frenet\ObjectType\Entity\Shipping\Info\ServiceInterface[] $services
     * @param string                                                     $name
     * @return bool|\Frenet\ObjectType\Entity\Shipping\Info\ServiceInterface
     */
    private function machServiceByName(array $services, $name)
    {
        /** @var \Frenet\ObjectType\Entity\Shipping\Info\ServiceInterface $service */
        foreach ($services as $service) {
            if (trim($name) == $service->getServiceDescription()) {
                return $service;
            }
        }

        return false;
    }

    /**
     * @param string $trackingNumber
     * @return array|null
     */
    private function getShipmentPossibleNames($trackingNumber)
    {
        /** @var Mage_Sales_Model_Order_Shipment_Track $track */
        $track = $this->getShipmentTrack($trackingNumber);

        if (empty($track)) {
            return null;
        }

        $shippingDescription = $track->getShipment()->getOrder()->getShippingDescription();
        $parts = explode(\Frenet\Shipping\Model\Carrier\Frenet::STR_SEPARATOR, $shippingDescription);

        /**
         * Reversing the array makes it more performatic because it begins searching by the last piece.
         */
        return (array) array_reverse($parts);
    }

    /**
     * @param string $trackingNumber
     * @return Mage_Sales_Model_Order_Shipment_Track
     */
    private function getShipmentTrack($trackingNumber)
    {
        /** @var Mage_Sales_Model_Resource_Order_Shipment_Track_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_shipment_track_collection');
        $collection->addFieldToFilter('track_number' ,$trackingNumber);

        foreach ($collection as $track) {
            return $track;
        }
    }
}
