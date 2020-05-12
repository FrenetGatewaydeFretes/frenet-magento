<?php

abstract class Frenet_Shipping_Controller_Front_Action extends Mage_Core_Controller_Front_Action
{
    /**
     * @param array $data
     *
     * @return string
     */
    protected function encodeResponse(array $data)
    {
        return json_encode($data);
    }
}
