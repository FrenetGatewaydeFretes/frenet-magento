<?php

/**
 * Class Frenet_Shipping_Model_Serializer
 */
class Frenet_Shipping_Model_Serializer implements Frenet_Shipping_Model_SerializerInterface
{
    /**
     * @param array $data
     * @return string
     */
    public function serialize(array $data)
    {
        return json_encode($data);
    }

    /**
     * @param string $string
     * @return array
     */
    public function unserialize($string)
    {
        return json_decode($string);
    }
}
