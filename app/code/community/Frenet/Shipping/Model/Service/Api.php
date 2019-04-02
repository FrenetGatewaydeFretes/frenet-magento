<?php

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
     * @var bool
     */
    private $isInitialized = false;

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

    private function init()
    {
        if (true === $this->isInitialized) {
            return;
        }

        $this->api = \Frenet\ApiFactory::create($this->objects()->config()->getToken());

        $this->initLogs();
        $this->isInitialized = true;
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function initLogs()
    {
        if (true == $this->objects()->config()->isDebugModeEnabled()) {
            $this->api
                ->config()
                ->debugger()
                ->isEnabled(true)
                ->setFilePath(Mage::getBaseDir('log'))
                ->setFilename($this->objects()->config()->getDebugFilename());
        }
    }
}
