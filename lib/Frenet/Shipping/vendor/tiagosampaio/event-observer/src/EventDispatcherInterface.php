<?php

namespace TiagoSampaio\EventObserver;

use TiagoSampaio\EventObserver\Observer\ObserverInterface;

/**
 * Class EventDispatcherInterface
 *
 * @package TiagoSampaio\Event
 */
interface EventDispatcherInterface
{
    /**
     * @param string $eventName
     * @param array  $eventData
     *
     * @return void
     */
    public function dispatch($eventName, array $eventData = []);
    
    /**
     * @param ObserverInterface $observer
     *
     * @return $this
     */
    public function addObserver(ObserverInterface $observer);
}
