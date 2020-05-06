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
 * Class Frenet_Shipping_Model_Rate_Request_Service
 */
class Frenet_Shipping_Model_Rate_Request_Service
{
    /**
     * @var RateRequest
     */
    private $rateRequest;

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
     * @return RateRequest|null
     */
    public function getRateRequest()
    {
        return $this->rateRequest;
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
