<?php
/**
 * Frenet Shipping Gateway
 *  
 * @category Frenet
 *  
 * @author Tiago Sampaio <tiago@tiagosampaio.com>
 * @link https://github.com/tiagosampaio
 * @link https://tiagosampaio.com
 *
 * Copyright (c) 2020.
 */

use Mage_Shipping_Model_Rate_Request as RateRequest;

/**
 * Class Frenet_Shipping_Model_Rate_Request_Provider
 */
class Frenet_Shipping_Model_Rate_Request_Provider
{
    /**
     * @var RateRequest
     */
    private $rateRequest;

    /**
     * @return RateRequest
     */
    public function createRateRequest()
    {
        return new RateRequest();
    }

    /**
     * @param RateRequest $rateRequest
     *
     * @return $this
     */
    public function setRateRequest(RateRequest $rateRequest)
    {
        $this->rateRequest = $rateRequest;
        return $this;
    }

    /**
     * @return RateRequest
     * @throws Mage_Exception
     */
    public function getRateRequest()
    {
        if ($this->rateRequest) {
            return $this->rateRequest;
        }

        throw new Mage_Exception('Rate Request is not set.');
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->rateRequest = null;
        return $this;
    }
}
