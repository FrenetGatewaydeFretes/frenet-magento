<?php

namespace TiagoSampaioTest\EventObserver;

use TiagoSampaio\EventObserver\Event;

/**
 * Class EventTest
 *
 * @package FrenetTest\Event
 */
class EventTest extends TestCase
{
    /**
     * @var Event
     */
    private $object;
    
    protected function setUp()
    {
        $this->object = $this->createObject(Event::class);
    }
    
    /**
     * @test
     */
    public function eventSetterAndGetter()
    {
        $eventName = 'this_is_an_test_event';
        
        $data = [
            'key_one' => 1,
            'key_two' => 2,
        ];
        
        $this->assertInstanceOf(Event::class, $this->object->setEvent($eventName, $data));
        
        $this->assertEquals($eventName, $this->object->getEventName());
        $this->assertEquals($data, $this->object->getData());
    }
}
