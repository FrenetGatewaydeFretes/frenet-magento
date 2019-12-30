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

use Mage_Shipping_Model_Rate_Request as RateRequest;

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
     * @var Frenet_Shipping_Model_Config
     */
    private $config;

    /**
     * @var Frenet_Shipping_Model_Quote_Item_Validator
     */
    private $quoteItemValidator;

    /**
     * @var Frenet_Shipping_Model_Quote_Item_Quantity_CalculatorInterface
     */
    private $itemQuantityCalculator;

    /**
     * @var Frenet_Shipping_Model_Formatters_Postcode_Normalizer
     */
    private $postcodeNormalizer;

    public function __construct()
    {
        $this->serializer = $this->objects()->serializer();
        $this->cache = Mage::app()->getCacheInstance();
        $this->config = $this->objects()->config();
        $this->quoteItemValidator = $this->objects()->quoteItemValidator();
        $this->itemQuantityCalculator = $this->objects()->quoteItemQtyCalculator();
        $this->postcodeNormalizer = $this->objects()->postcodeNormalizer();
    }

    /**
     * @param RateRequest $request
     *
     * @return bool
     */
    public function load(RateRequest $request)
    {
        if (!$this->isCacheEnabled()) {
            return false;
        }

        $data = $this->cache->load($this->generateCacheKey($request));

        if ($data) {
            $data = $this->prepareAfterLoading($data);
        }

        return $data;
    }

    /**
     * @param array       $services
     * @param RateRequest $request
     *
     * @return bool
     */
    public function save(array $services, RateRequest $request)
    {
        if (!$this->isCacheEnabled()) {
            return false;
        }

        $identifier = $this->generateCacheKey($request);
        $lifetime = null;
        $tags = [self::CACHE_TAG];

        return $this->cache->save($this->prepareBeforeSaving($services), $identifier, $tags, $lifetime);
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
     * @return string
     */
    private function generateCacheKey(RateRequest $request)
    {
        $destPostcode = $request->getDestPostcode();
        $origPostcode = $this->config->getOriginPostcode();
        $items = [];

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($request->getAllItems() as $item) {
            if (!$this->quoteItemValidator->validate($item)) {
                continue;
            }

            $productId = (int) $item->getProductId();

            if ($item->getParentItem()) {
                $productId = $item->getParentItem()->getProductId() . '-' . $productId;
            }

            $qty = (float) $this->itemQuantityCalculator->calculate($item);

            $items[$productId] = $qty;
        }

        ksort($items);

        $cacheKey = $this->serializer->serialize([
            $this->postcodeNormalizer->format($origPostcode),
            $this->postcodeNormalizer->format($destPostcode),
            $items,
            $this->config->isMultiQuoteEnabled() ? 'multi' : null
        ]);

        return $cacheKey;
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
