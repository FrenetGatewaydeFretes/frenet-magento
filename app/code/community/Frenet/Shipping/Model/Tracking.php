<?php

/**
 * Class Frenet_Shipping_Model_Tracking
 */
class Frenet_Shipping_Model_Tracking implements Frenet_Shipping_Model_TrackingInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Frenet_Shipping_Model_Service_Api
     */
    private $apiService;

    /**
     * Tracking constructor.
     */
    public function __construct()
    {
        $this->apiService = $this->objects()->apiService();
    }

    /**
     * @inheritdoc
     */
    public function track($number, $shippingServiceCode)
    {
        /** @var \Frenet\Command\Tracking\TrackingInfoInterface $tracking */
        $tracking = $this->apiService
            ->tracking()
            ->trackingInfo()
            ->setShippingServiceCode($shippingServiceCode)
            ->setTrackingNumber($number);

        /** @var \Frenet\ObjectType\Entity\Tracking\TrackingInfoInterface $info */
        $info = $tracking->execute();

        return $info;
    }
}
