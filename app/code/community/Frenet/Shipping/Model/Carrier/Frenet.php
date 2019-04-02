<?php

use Frenet\ObjectType\Entity\Shipping\Quote\ServiceInterface as QuoteServiceInterface;

/**
 * Class Frenet_Shipping_Model_Carrier_Frenet
 */
class Frenet_Shipping_Model_Carrier_Frenet extends Mage_Shipping_Model_Carrier_Abstract
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var string
     */
    const CARRIER_CODE = 'frenetshipping';

    /**
     * @var string
     */
    const STR_SEPARATOR = ' - ';

    /**
     * @var string
     */
    protected $_code = self::CARRIER_CODE;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var Mage_Shipping_Model_Rate_Result
     */
    private $rateResult;

    /**
     * @var Mage_Shipping_Model_Tracking_Result
     */
    private $trackingResult;

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool|Mage_Shipping_Model_Rate_Result|null
     * @throws Mage_Core_Model_Store_Exception
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->canCollectRates()) {
            $errorMessage = $this->getErrorMessage();
            Mage::log("Frenet canCollectRates: " . $errorMessage);
            return $errorMessage;
        }

        /** @var array $results */
        if (!$results = $this->objects()->calculator()->getQuote($request)) {
            return $this->rateResult;
        }

        $this->prepareRateResult($results);

        return $this->rateResult;
    }

    /**
     * Checks if shipping method is correctly configured
     *
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function canCollectRates()
    {
        /** Validate carrier active flag */
        if (!$this->objects()->config()->isActive()) {
            return false;
        }

        /** @var int $store */
        $store = Mage::app()->getStore();

        /** Validate origin postcode */
        if (!$this->objects()->config()->getOriginPostcode($store)) {
            return false;
        }

        /** Validate frenet token */
        if (!$this->objects()->config()->getToken()) {
            return false;
        }

        return true;
    }

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool|Frenet_Shipping_Model_Carrier_Frenet|Mage_Shipping_Model_Carrier_Abstract|Mage_Shipping_Model_Rate_Result_Error|\Magento\Framework\DataObject
     */
    public function proccessAdditionalValidation(Mage_Shipping_Model_Rate_Request $request)
    {
        return $this->processAdditionalValidation($request);
    }

    /**
     * Processing additional validation (quote data) to check if carrier applicable.
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     *
     * @return $this|array|Mage_Shipping_Model_Rate_Result_Error
     */
    public function processAdditionalValidation(Mage_Shipping_Model_Rate_Request $request)
    {
        /** Validate request items data */
        if (empty($request->getAllItems())) {
            $this->errors[] = Mage::helper('frenet_shipping')->__('There is no items in this order');
        }

        /** Validate destination postcode */
        if (!$request->getDestPostcode()) {
            $this->errors[] = Mage::helper('frenet_shipping')->__('Please inform the destination postcode');
        }

        /** Validate destination postcode */
        if (!((int) Mage::helper('frenet_shipping')->normalizePostcode($request->getDestPostcode()))) {
            $this->errors[] = Mage::helper('frenet_shipping')->__('Please inform a valid postcode');
        }

        if (!empty($this->errors)) {
            /** @var Mage_Shipping_Model_Rate_Result_Error  $error */
            $error = Mage::getModel('shipping/rate_result_error', array(
                'carrier'       => $this->_code,
                'carrier_title' => $this->objects()->config()->getCarrierConfig('title'),
                'error_message' => implode(', ', $this->errors)
            ));

            $this->debugErrors($error);

            return $error;
        }

        return $this;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     * @api
     */
    public function getAllowedMethods()
    {
        return [self::CARRIER_CODE => $this->objects()->config()->getCarrierConfig('name')];
    }

    /**
     * @param array|string $trackingNumbers
     * @return Mage_Shipping_Model_Tracking_Result|null
     */
    public function getTrackingInfo($trackingNumbers)
    {
        if (!is_array($trackingNumbers)) {
            $trackingNumbers = [$trackingNumbers];
        }

        $this->prepareTrackingResult($trackingNumbers);

        /** @var Mage_Shipping_Model_Tracking_Result_Status $tracking */
        foreach ($this->trackingResult->getAllTrackings() as $tracking) {
            return $tracking;
        }

        return $this->trackingResult;
    }

    /**
     * @return bool
     */
    public function isTrackingAvailable()
    {
        return true;
    }

    /**
     * @param array $trackingNumbers
     * @return Mage_Shipping_Model_Tracking_Result
     */
    private function prepareTrackingResult(array $trackingNumbers)
    {
        /** @var Mage_Shipping_Model_Tracking_Result $result */
        $result = Mage::getModel('shipping/tracking_result');

        /** @var string $trackingNumber */
        foreach ($trackingNumbers as $trackingNumber) {
            /** @var \Frenet\ObjectType\Entity\Shipping\Info\ServiceInterface $service */
            $service = $this->objects()->serviceFinder()->findByTrackingNumber($trackingNumber);
            $serviceCode = $service ? $service->getServiceCode() : null;

            /** @var Mage_Shipping_Model_Tracking_Result_Status $status */
            $status = Mage::getModel('shipping/tracking_result_status');

            $status->setCarrier(self::CARRIER_CODE);
            $status->setCarrierTitle($this->getConfigData('title'));
            $status->setTracking($trackingNumber);
            $status->setPopup(1);
            $status->setTrackSummary($this->prepareTrackingInformation($status, $trackingNumber, $serviceCode));
            $result->append($status);
        }

        $this->trackingResult = $result;

        return $result;
    }

    /**
     * @param Mage_Shipping_Model_Tracking_Result_Status $status
     * @param string                                     $trackingNumber
     * @param string                                     $shippingServiceCode
     */
    private function prepareTrackingInformation($status, $trackingNumber, $shippingServiceCode)
    {
        /** @var \Frenet\ObjectType\Entity\Tracking\TrackingInfoInterface $trackingInfo */
        $trackingInfo = $this->objects()->trackingService()->track($trackingNumber, $shippingServiceCode);

        $events = $trackingInfo->getTrackingEvents();

        if (empty($events)) {
            return;
        }

        /** @var \Frenet\ObjectType\Entity\Tracking\TrackingInfo\EventInterface $event */
        $event = end($events);

        $status->setStatus($event->getEventDescription());
        $status->setDeliveryLocation($event->getEventLocation());
        $status->setShippedDate($event->getEventDatetime());
        $status->setService($event->getTrackingInfo()->getServiceDescription());
    }

    /**
     * @param array $items
     *
     * @return $this
     */
    private function prepareRateResult(array $items = [])
    {
        /** @var Mage_Shipping_Model_Rate_Result $result */
        $this->rateResult = Mage::getModel('shipping/rate_result');

        /** @var QuoteServiceInterface $item */
        foreach ($items as $item) {
            if ($item->isError()) {
                continue;
            }

            $deliveryTime = (int) $item->getDeliveryTime();
            $additionalLeadTime = (int) $this->objects()->config()->getAdditionalLeadTime();

            $item->setData(QuoteServiceInterface::FIELD_DELIVERY_TIME, $deliveryTime + $additionalLeadTime);

            $title = $this->prepareMethodTitle(
                $item->getCarrier(),
                $item->getServiceDescription(),
                $item->getDeliveryTime()
            );

            $method = $this->prepareMethod(
                $title,
                $item->getServiceCode(),
                $item->getServiceDescription(),
                $item->getShippingPrice(),
                $item->getShippingPrice()
            );

            $this->rateResult->append($method);
        }

        return $this;
    }

    /**
     * @param string $method
     * @param string $code
     * @param string $title
     * @param float  $price
     * @param float  $cost
     *
     * @return Mage_Shipping_Model_Rate_Result_Method
     */
    private function prepareMethod($method, $code, $title, $price, $cost)
    {
        /** @var Mage_Shipping_Model_Rate_Result_Method $methodInstance */
        $methodInstance = Mage::getModel('shipping/rate_result_method');
        $methodInstance->setCarrier($this->_code)
            ->setCarrierTitle($this->objects()->config()->getCarrierConfig('title'))
            ->setMethod($method)
            ->setMethodTitle($title)
            ->setMethodDescription($code)
            ->setPrice($price)
            ->setCost($cost);

        return $methodInstance;
    }

    /**
     * @param string $carrier
     * @param string $description
     * @param int    $leadTime
     *
     * @return string
     */
    private function prepareMethodTitle($carrier, $description, $leadTime = 0)
    {
        $title = Mage::helper('frenet_shipping')->__('%s' . self::STR_SEPARATOR . '%s', $carrier, $description);

        if ($this->objects()->config()->canShowShippingForecast()) {
            $message = str_replace('{{d}}', (int) $leadTime, $this->objects()->config()->getShippingForecast());
            $title .= self::STR_SEPARATOR . $message;
        }

        return $title;
    }
}
