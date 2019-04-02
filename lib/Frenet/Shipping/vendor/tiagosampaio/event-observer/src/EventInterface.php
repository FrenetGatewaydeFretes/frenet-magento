<?php

namespace TiagoSampaio\EventObserver;

/**
 * Class EventDataInterface
 *
 * @package TiagoSampaio\Event
 */
interface EventInterface
{
    /**
     * @return string
     */
    public function getEventName();
    
    /**
     * @param string $name
     * @param array  $eventData
     *
     * @return $this
     */
    public function setEvent($name, array $eventData = []);
}
