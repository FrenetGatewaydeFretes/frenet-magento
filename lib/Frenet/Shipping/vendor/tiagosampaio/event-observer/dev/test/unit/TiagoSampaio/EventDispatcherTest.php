<?php

namespace TiagoSampaioTest\EventObserver;

use TiagoSampaio\EventObserver\EventDispatcher;

/**
 * Class EventDispatcherTest
 *
 * @package FrenetTest\Event
 */
class EventDispatcherTest extends TestCase
{
    /**
     * @var EventDispatcher
     */
    private $object;
    
    protected function setUp()
    {
        $this->object = $this->createObject(EventDispatcher::class);
    }
    
    /**
     * @test
     */
    public function addObserver()
    {
//        $this->assertInstanceOf(
//            \Frenet\Event\EventDispatcher::class,
//            $this->object->addObserver($this->observerFactory->createRequestResultLogger())
//        );
    }
    
    /**
     * @test
     */
    public function dispatch()
    {
//        $observer = $this->createMock(\Frenet\Event\Observer\ObserverInterface::class);
//        $observer->expects($this->once())->method('execute');
        
//        $this->object->addObserver($observer);
        
//        $this->assertNull(
//            $this->object->dispatch('connection_request_result', ['data_one' => 'value_one'])
//        );
    }
}
