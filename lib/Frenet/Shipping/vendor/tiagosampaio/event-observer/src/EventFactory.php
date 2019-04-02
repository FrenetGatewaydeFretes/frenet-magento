<?php

namespace TiagoSampaio\EventObserver;

/**
 * Class EventDataFactory
 *
 * @package TiagoSampaio\Event
 */
class EventFactory
{
    /**
     * @return Event
     */
    public function create()
    {
        return new Event();
    }
}
