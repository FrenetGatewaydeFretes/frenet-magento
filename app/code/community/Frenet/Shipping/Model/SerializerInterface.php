<?php

/**
 * Class Frenet_Shipping_Model_SerializerInterface
 */
interface Frenet_Shipping_Model_SerializerInterface
{
    /**
     * @param array $data
     * @return string
     */
    public function serialize(array $data);

    /**
     * @param string $string
     * @return array
     */
    public function unserialize($string);
}
