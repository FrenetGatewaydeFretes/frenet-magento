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

use Frenet\ObjectType\Entity\Shipping\Quote\Service;
use Mage_Shipping_Model_Rate_Request as RateRequest;
use Frenet_Shipping_Model_Packages_Package as Package;
use Frenet_Shipping_Model_Packages_Package_Manager as PackageManager;
use Frenet_Shipping_Model_Packages_Package_Matching as PackageMatching;
use Frenet_Shipping_Model_Packages_Package_Limit as PackageLimit;
use Frenet_Shipping_Model_Packages_Package_Processor as PackageProcessor;
use Frenet_Shipping_Model_Quote_Multi_Quote_Validator as MultiQuoteValidatorInterface;
use Frenet_Shipping_Model_Rate_Request_Provider as RateRequestProvider;

/**
 * Class Frenet_Shipping_Model_Packages_Package_Calculator
 */
class Frenet_Shipping_Model_Packages_Package_Calculator
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var PackageManager
     */
    private $packageManager;

    /**
     * @var MultiQuoteValidatorInterface
     */
    private $multiQuoteValidator;

    /**
     * @var PackageLimit
     */
    private $packageLimit;

    /**
     * @var PackageMatching
     */
    private $packageMatching;

    /**
     * @var PackageProcessor
     */
    private $packageProcessor;

    /**
     * @var RateRequestProvider
     */
    private $rateRequestProvider;

    public function __construct()
    {
        $this->packageManager = $this->objects()->packageManager();
        $this->multiQuoteValidator = $this->objects()->quoteMultiQuoteValidator();
        $this->packageLimit = $this->objects()->packageLimit();
        $this->packageMatching = $this->objects()->packageMatching();
        $this->packageProcessor = $this->objects()->packageProcessor();
        $this->rateRequestProvider = $this->objects()->rateRequestProvider();
    }

    /**
     * @return Service[]
     */
    public function calculate()
    {
        /** @var RateRequest $rateRequest */
        $rateRequest = $this->rateRequestProvider->getRateRequest();
        $this->packageManager->resetPackages();

        /**
         * If the package is not overweight then we simply process all the package.
         */
        if (!$this->packageLimit->isOverWeight((float) $rateRequest->getPackageWeight())) {
            return $this->processPackages();
        }

        /**
         * If the multi quote is disabled, we remove the limit.
         */
        if (!$this->multiQuoteValidator->canProcessMultiQuote()) {
            $this->packageLimit->removeLimit();
            return $this->processPackages();
        }

        /**
         * Make a full call first because of the other companies that don't have weight limit like Correios.
         */
        $this->packageLimit->removeLimit();
        $this->packageManager->process();
        $this->packageManager->unsetCurrentPackage();

        /**
         * Reset the limit so the next process will split the cart into packages.
         */
        $this->packageLimit->resetMaxWeight();
        $packages = $this->processPackages();

        /**
         * Package Matching binds the results for Correios only.
         * The other options (not for Correios) are got from the full call (the first one).
         */
        return $this->packageMatching->match($packages);
    }

    /**
     * @return Service[]
     */
    private function processPackages()
    {
        $this->packageManager->process();
        $results = [];

        /** @var Package $package */
        foreach ($this->packageManager->getPackages() as $key => $package) {
            /** @var Service[] $services */
            $services = $this->packageProcessor->process($package);

            /**
             * If there's only one package then we can simply return the services quote.
             */
            if ($this->packageManager->countPackages() == 1) {
                return $services;
            }

            /**
             * Otherwise we need to bind the quotes.
             */
            $results[$key] = $services;
        }

        return $results;
    }
}
