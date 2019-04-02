<?php

namespace TiagoSampaio\EventObserver;

use TiagoSampaio\EventObserver\Observer\ObserverInterface;

/**
 * Class EventDispatcher
 *
 * @package TiagoSampaio\Event
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var array
     */
    private $observers = [];
    
    /**
     * @var EventFactory
     */
    private $eventFactory;
    
    /**
     * EventDispatcher constructor.
     */
    public function __construct()
    {
        $this->eventFactory = new EventFactory();
    }
    
    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, array $eventData = [])
    {
        /** @var Observer\ObserverInterface $observer */
        foreach ($this->observers as $observer) {
            /** @var EventInterface $data */
            $event = $this->eventFactory->create();
            $event->setEvent($eventName, (array) $eventData);
            $observer->execute($event);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function addObserver(ObserverInterface $observer)
    {
        $this->observers[] = $observer;
        return $this;
    }
}
