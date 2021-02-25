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

/**
 * Class CacheManager
 *
 * @package Frenet\Shipping\Model
 */
class Frenet_Shipping_Model_Cache_Manager
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * Cache type code unique among all cache types
     */
    const TYPE_IDENTIFIER = 'frenet_api_result';

    /**
     * Cache tag used to distinguish the cache type from all other cache
     */
    const CACHE_TAG = 'FRENET_API_RESULT';

    /**
     * @var Mage_Core_Model_Cache
     */
    private $cache;

    /**
     * @var Frenet_Shipping_Model_Serializer
     */
    private $serializer;

    /**
     * @var Frenet_Shipping_Model_Cache_Key_Generator
     */
    private $cacheKeyGenerator;

    public function __construct()
    {
        $this->serializer = $this->objects()->serializer();
        $this->cache = Mage::app()->getCacheInstance();
        $this->cacheKeyGenerator = $this->objects()->cacheKeyGenerator();
    }

    /**
     * @return array|bool|string
     */
    public function load()
    {
        if (!$this->isCacheEnabled()) {
            return false;
        }

        $data = $this->cache->load($this->cacheKeyGenerator->generate());

        if ($data) {
            $data = $this->prepareAfterLoading($data);
        }

        return $data;
    }

    /**
     * @param array $services
     *
     * @return bool
     */
    public function save(array $services)
    {
        if (!$this->isCacheEnabled()) {
            return false;
        }

        $identifier = $this->cacheKeyGenerator->generate();
        $lifetime = null;
        $tags = [self::CACHE_TAG];

        return $this->cache->save(
            $this->prepareBeforeSaving($services),
            $identifier,
            $tags,
            $lifetime
        );
    }

    /**
     * @param $data
     *
     * @return array
     */
    private function prepareAfterLoading($data)
    {
        $newData  = [];
        $services = $this->serializer->unserialize($data);

        /** @var array $service */
        foreach ($services as $service) {
            $newData[] = $this->createServiceInstance()->setData($service);
        }

        return $newData;
    }

    /**
     * @param array $services
     *
     * @return bool|string
     */
    private function prepareBeforeSaving(array $services)
    {
        $newData = [];

        /** @var \Frenet\ObjectType\Entity\Shipping\QuoteInterface $service */
        foreach ($services as $service) {
            $newData[] = $service->getData();
        }

        return $this->serializer->serialize($newData);
    }

    /**
     * @return bool
     */
    private function isCacheEnabled()
    {
        return (bool) Mage::app()->useCache(self::TYPE_IDENTIFIER);
    }

    /**
     * @return \Frenet\ObjectType\Entity\Shipping\Quote\Service
     */
    private function createServiceInstance()
    {
        return new \Frenet\ObjectType\Entity\Shipping\Quote\Service(
            new \Frenet\Framework\Data\Serializer()
        );
    }
}
