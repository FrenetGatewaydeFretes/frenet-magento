<?php

use Frenet\ObjectType\Entity\Shipping\Quote\ServiceInterface as QuoteServiceInterface;
use Mage_Shipping_Model_Rate_Request as RateRequest;

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
     * @var Frenet_Shipping_Model_Store_Management
     */
    private $storeManagement;

    /**
     * @var Frenet_Shipping_Model_Factory_Product_Resource
     */
    private $productResourceFactory;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var Mage_Shipping_Model_Rate_Result
     */
    private $result;

    /**
     * @var Frenet_Shipping_Model_CalculatorInterface
     */
    private $calculator;

    /**
     * @var Frenet_Shipping_Model_Delivery_Time_Calculator
     */
    private $deliveryTimeCalculator;

    /**
     * @var Frenet_Shipping_Model_TrackingInterface
     */
    private $trackingService;

    /**
     * @var Frenet_Shipping_Model_Service_FinderInterface
     */
    private $serviceFinder;

    /**
     * @var Frenet_Shipping_Model_Formatters_Postcode_Normalizer
     */
    private $postcodeNormalizer;

    /**
     * @var Frenet_Shipping_Model_Config
     */
    private $config;

    public function __construct()
    {
        parent::__construct();

        $this->storeManagement = $this->objects()->storeManagement();
        $this->productResourceFactory = $this->objects()->productResourceFactory();
        $this->trackingService = $this->objects()->trackingService();
        $this->calculator = $this->objects()->calculator();
        $this->serviceFinder = $this->objects()->serviceFinder();
        $this->config = $this->objects()->config();
        $this->deliveryTimeCalculator = $this->objects()->deliveryTimeCalculator();
        $this->postcodeNormalizer = $this->objects()->postcodeNormalizer();
    }

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool|Mage_Shipping_Model_Rate_Result|null
     * @throws Mage_Core_Model_Store_Exception
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->canCollectRates()) {
            $errorMessage = $this->getErrorMessage();
            $this->_logger->debug("Frenet canCollectRates: " . $errorMessage);

            return $errorMessage;
        }

        /** @var array $results */
        if (!$results = $this->calculator->getQuote($request)) {
            return $this->result;
        }

        $this->prepareResult($request, $results);

        return $this->result;
    }

    /**
     * Checks if shipping method is correctly configured
     *
     * @return bool
     */
    public function canCollectRates()
    {
        /** Validate carrier active flag */
        if (!$this->config->isActive()) {
            return false;
        }

        /** @var int $store */
        $store = $this->getStore();

        /** Validate origin postcode */
        if (!$this->config->getOriginPostcode($store)) {
            return false;
        }

        /** Validate frenet token */
        if (!$this->config->getToken()) {
            return false;
        }

        return true;
    }

    /**
     * Make this module compatible with older versions of Magento 2.
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     *
     * @return $this|bool|Mage_Shipping_Model_Carrier_Abstract|Mage_Shipping_Model_Rate_Result_Error
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
     * @return $this|Mage_Shipping_Model_Rate_Result_Error
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
        if (!((int) $this->postcodeNormalizer->format($request->getDestPostcode()))) {
            $this->errors[] = Mage::helper('frenet_shipping')->__('Please inform a valid postcode');
        }

        if (!empty($this->errors)) {
            /** @var Mage_Shipping_Model_Rate_Result_Error $error */
            $error = $this->_rateErrorFactory->create([
                'carrier'       => $this->_code,
                'carrier_title' => $this->config->getCarrierConfig('title'),
                'error_message' => implode(', ', $this->errors)
            ]);

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
        return [self::CARRIER_CODE => $this->config->getCarrierConfig('name')];
    }

    /**
     * @param $trackingNumbers
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function getTracking($trackingNumbers)
    {
        if (!is_array($trackingNumbers)) {
            $trackingNumbers = [$trackingNumbers];
        }

        $this->prepareTracking($trackingNumbers);

        return $this->result;
    }

    /**
     * @param array $trackingNumbers
     *
     * @return Mage_Shipping_Model_Tracking_Result
     */
    private function prepareTracking(array $trackingNumbers)
    {
        /** @var Mage_Shipping_Model_Tracking_Result $result */
        $result = Mage::getModel('shipping/tracking_result');

        /** @var string $trackingNumber */
        foreach ($trackingNumbers as $trackingNumber) {
            /** @var \Frenet\ObjectType\Entity\Shipping\Info\ServiceInterface $service */
            $service = $this->serviceFinder->findByTrackingNumber($trackingNumber);
            $serviceCode = $service ? $service->getServiceCode() : null;

            /** @var Mage_Shipping_Model_Tracking_Result_Status $status */
            $status = $this->_trackStatusFactory->create();
            $status->setCarrier(self::CARRIER_CODE);
            $status->setCarrierTitle($this->getConfigData('title'));
            $status->setTracking($trackingNumber);
            $status->setPopup(1);
            $status->setTrackSummary($this->prepareTrackingInformation($status, $trackingNumber, $serviceCode));
            $result->append($status);
        }

        $this->result = $result;

        return $result;
    }

    /**
     * @param Mage_Shipping_Model_Tracking_Result_Status $status
     * @param string                                     $trackingNumber
     * @param string                                     $shippingServiceCode
     *
     * @return void
     */
    private function prepareTrackingInformation($status, $trackingNumber, $shippingServiceCode)
    {
        /** @var \Frenet\ObjectType\Entity\Tracking\TrackingInfoInterface $trackingInfo */
        $trackingInfo = $this->trackingService->track($trackingNumber, $shippingServiceCode);

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
     * @param RateRequest             $request
     * @param QuoteServiceInterface[] $items
     *
     * @return $this
     */
    private function prepareResult(RateRequest $request, array $services = [])
    {
        /** @var Mage_Shipping_Model_Rate_Result $result */
        $this->result = Mage::getModel('shipping/rate_result');

        /** @var QuoteServiceInterface $service */
        foreach ($services as $service) {
            if ($service->isError()) {
                continue;
            }

            $deliveryTime = $this->deliveryTimeCalculator->calculate($request, $service);

            $methodTitle = $this->prepareMethodTitle(
                $service->getCarrier(),
                $service->getServiceDescription(),
                $deliveryTime
            );

            $method = $this->prepareMethod(
                $methodTitle,
                $service->getServiceCode(),
                $this->appendInformation($service->getServiceDescription(), $deliveryTime, $service->getMessage()),
                $service->getShippingPrice(),
                $service->getShippingPrice()
            );

            $this->result->append($method);
        }

        return $this;
    }

    /**
     * @return Mage_Core_Model_Store
     */
    private function getStore()
    {
        try {
            return $this->storeManagement->getStore();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param string $method
     * @param string $code
     * @param string $methodTitle
     * @param float  $price
     * @param float  $cost
     *
     * @return Mage_Shipping_Model_Rate_Result_Method
     */
    private function prepareMethod($method, $code, $methodTitle, $price, $cost)
    {
        /** @var Mage_Shipping_Model_Rate_Result_Method $methodInstance */
        $methodInstance = Mage::getModel('shipping/rate_result_method');
        $methodInstance->setCarrier($this->_code)
            ->setCarrierTitle($this->config->getCarrierConfig('title'))
            ->setMethod($method)
            ->setMethodTitle($methodTitle)
            ->setMethodDescription($code)
            ->setPrice($price)
            ->setCost($cost);

        return $methodInstance;
    }

    /**
     * @param string $carrier
     * @param string $description
     * @param int    $deliveryTime
     * @param string $message
     *
     * @return string
     */
    private function prepareMethodTitle($carrier, $description, $deliveryTime = 0)
    {
        $title = Mage::helper('frenet_shipping')->__('%s' . self::STR_SEPARATOR . '%s', $carrier, $description);
        $title = $this->appendInformation($title, $deliveryTime);

        return $title;
    }

    /**
     * @param string $text
     * @param int    $deliveryTime
     * @param string $message
     *
     * @return string
     */
    private function appendInformation($text, $deliveryTime = 0, $message = null)
    {
        if ($this->config->canShowShippingForecast()) {
            $text .= self::STR_SEPARATOR . $this->getDeliveryTimeMessage($deliveryTime);
        }

        /**
         * In some cases the API returns some messages about restrictions or extended delivery time.
         * This is where this information will be displayed.
         */
        if ($message) {
            $text .= " ({$message})";
        }

        return $text;
    }

    /**
     * @param int $deliveryTime
     *
     * @return mixed
     */
    private function getDeliveryTimeMessage($deliveryTime = 0)
    {
        return str_replace('{{d}}', (int) $deliveryTime, $this->config->getShippingForecastMessage());
    }
}
