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

class Frenet_Shipping_Model_Cache_Key_Generator_Postcode
    implements Frenet_Shipping_Model_Cache_Key_GeneratorInterface
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Frenet_Shipping_Model_Rate_Request_Provider
     */
    private $requestProvider;

    /**
     * @var Frenet_Shipping_Model_Config
     */
    private $config;

    /**
     * @var Frenet_Shipping_Model_SerializerInterface
     */
    private $serializer;

    /**
     * @var Frenet_Shipping_Model_Formatters_Postcode_Normalizer
     */
    private $postcodeNormalizer;

    public function __construct()
    {
        $this->serializer = $this->objects()->serializer();
        $this->requestProvider = $this->objects()->rateRequestProvider();
        $this->config = $this->objects()->config();
        $this->postcodeNormalizer = $this->objects()->postcodeNormalizer();
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $destPostcode = $this->requestProvider->getRateRequest()->getDestPostcode();
        $origPostcode = $this->config->getOriginPostcode();

        return $this->postcodeNormalizer->format($destPostcode) . '-' .
            $this->postcodeNormalizer->format($origPostcode);
    }
}
