<?php

/**
 * Class Frenet_Shipping_Model_Tracking
 */
class Frenet_Shipping_Model_Tracking implements Frenet_Shipping_Model_TrackingInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @inheritdoc
     */
    public function track($number, $shippingServiceCode)
    {
        /** @var \Frenet\Command\Tracking\TrackingInfoInterface $tracking */
        $tracking = $this->objects()
            ->apiService()
            ->tracking()
            ->trackingInfo()
            ->setShippingServiceCode($shippingServiceCode)
            ->setTrackingNumber($number);

        /** @var \Frenet\ObjectType\Entity\Tracking\TrackingInfoInterface $info */
        $info = $tracking->execute();

        return $info;
    }
}
