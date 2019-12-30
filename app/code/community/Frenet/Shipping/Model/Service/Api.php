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

Frenet_Shipping_Model_DependencyFinder::includeDependency();

/**
 * Class Frenet_Shipping_Model_Service_Api
 */
class Frenet_Shipping_Model_Service_Api
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var \Frenet\ApiInterface
     */
    private $api;

    /**
     * @var Frenet_Shipping_Model_Config
     */
    private $config;

    /**
     * @var bool
     */
    private $isInitialized = false;

    public function __construct()
    {
        $this->config = $this->objects()->config();
    }

    /**
     * @inheritdoc
     */
    public function postcode()
    {
        $this->init();
        return $this->api->postcode();
    }

    /**
     * @inheritdoc
     */
    public function tracking()
    {
        $this->init();
        return $this->api->tracking();
    }

    /**
     * @inheritdoc
     */
    public function shipping()
    {
        $this->init();
        return $this->api->shipping();
    }

    /**
     * Initializes the API Service.
     *
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    private function init()
    {
        if (true === $this->isInitialized) {
            return;
        }

        $this->api = \Frenet\ApiFactory::create($this->config->getToken());

        $this->initLogs();
        $this->isInitialized = true;
    }

    private function initLogs()
    {
        if (true == $this->config->isDebugModeEnabled()) {
            $this->api
                ->config()
                ->debugger()
                ->isEnabled(true)
                ->setFilePath(Mage::getBaseDir('log'))
                ->setFilename($this->config->getDebugFilename());
        }
    }
}
