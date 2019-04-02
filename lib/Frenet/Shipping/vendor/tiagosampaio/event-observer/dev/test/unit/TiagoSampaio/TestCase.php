<?php

namespace TiagoSampaioTest\EventObserver;

/**
 * Class TestCase
 * @package FrenetTest
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @param       $objectClass
     * @param array $parameters
     * @return mixed
     */
    protected function createObject($objectClass, array $parameters = [])
    {
        return new $objectClass(...$parameters);
    }
}
