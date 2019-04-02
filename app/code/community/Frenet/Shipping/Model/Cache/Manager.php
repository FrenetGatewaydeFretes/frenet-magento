<?php

/**
 * Class Frenet_Shipping_Model_Cache_Manager
 */
class Frenet_Shipping_Model_Cache_Manager
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * Cache type code unique among all cache types
     */
    const TYPE_IDENTIFIER = 'frenet_result';

    /**
     * Cache tag used to distinguish the cache type from all other cache
     */
    const CACHE_TAG = 'FRENET_RESULT';

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     *
     * @return bool
     */
    public function load(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->isCacheEnabled()) {
            return false;
        }

        $data = $this->objects()->cache()->load($this->generateCacheKey($request));

        if ($data) {
            $data = $this->prepareAfterLoading($data);
        }

        return $data;
    }

    /**
     * @param array                            $services
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool
     */
    public function save(array $services, Mage_Shipping_Model_Rate_Request $request)
    {
        $identifier = $this->generateCacheKey($request);
        $lifetime = null;
        $tags = array(self::CACHE_TAG);

        return $this->objects()->cache()->save($this->prepareBeforeSaving($services), $identifier, $tags, $lifetime);
    }

    /**
     * @param $data
     *
     * @return array
     */
    private function prepareAfterLoading($data)
    {
        $newData = array();

        $services = $this->objects()->serializer()->unserialize($data);

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
        $newData = array();

        /** @var \Frenet\ObjectType\Entity\Shipping\QuoteInterface $services */
        foreach ($services as $service) {
            $newData[] = $service->getData();
        }

        return $this->objects()->serializer()->serialize($newData);
    }

    private function generateCacheKey(Mage_Shipping_Model_Rate_Request $request)
    {
        $destPostcode = $request->getDestPostcode();
        $origPostcode = $this->objects()->config()->getOriginPostcode();
        $items = array();

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($request->getAllItems() as $item) {
            if (!$this->objects()->quoteItemValidator()->validate($item)) {
                continue;
            }

            $productId = (int) $item->getProductId();

            if ($item->getParentItem()) {
                $productId = $item->getParentItem()->getProductId() . '-' . $productId;
            }

            $qty = (float) $item->getQty();

            $items[$productId] = $qty;
        }

        ksort($items);

        $cacheKey = $this->objects()->serializer()->serialize(array(
            Mage::helper('frenet_shipping')->normalizePostcode($origPostcode),
            Mage::helper('frenet_shipping')->normalizePostcode($destPostcode),
            $items
        ));

        return $cacheKey;
    }

    /**
     * @return bool
     */
    private function isCacheEnabled()
    {
        return (bool) $this->objects()->cache()->canUse(self::TYPE_IDENTIFIER);
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
