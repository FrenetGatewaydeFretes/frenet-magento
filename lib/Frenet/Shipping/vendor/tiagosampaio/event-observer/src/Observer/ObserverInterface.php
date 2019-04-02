<?php

namespace TiagoSampaio\EventObserver\Observer;

use TiagoSampaio\EventObserver\EventInterface;

/**
 * Class ObserverInterface
 *
 * @package TiagoSampaio\Event\Observer
 */
interface ObserverInterface
{
    /**
     * @param EventInterface $event
     */
    public function execute(EventInterface $event);
}
