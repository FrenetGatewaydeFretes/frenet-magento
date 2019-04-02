<?php

namespace TiagoSampaio\EventObserver;

use TiagoSampaio\DataObject;

/**
 * Class EventData
 *
 * @package TiagoSampaio\Event
 */
class Event extends DataObject implements EventInterface
{
    /**
     * @var string
     */
    private $name = null;
    
    /**
     * {@inheritdoc}
     */
    public function getEventName()
    {
        return $this->name;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setEvent($name, array $eventData = [])
    {
        $this->name = $name;
        $this->setData($eventData);
        return $this;
    }
}
