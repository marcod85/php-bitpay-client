<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Resource;

/**
 * Class that can retrieve and parse the various
 * currency codes and related parameters. The
 * structure returned from BitPay looks like:
 *
 * stdClass Object
 * (
 *    [code]           => ZAR
 *    [symbol]         => R
 *    [precision]      => 2
 *    [exchangePctFee] => 100
 *    [payoutEnabled]  => 1
 *    [name]           => South African Rand
 *    [plural]         => South African Rand
 *    [alts]           => zar
 *    [minimum]        => 0.01
 *    [payoutFields]   => Array
 *       (
 *           [0] => name
 *           [1] => account
 *           [2] => bank
 *           [3] => swift
 *           [4] => address
 *           [5] => city
 *           [6] => postal
 *       )
 * )
 *
 * Note: The payoutFields array is usually empty
 * but, if it's present, is currency-specific.
 * @see https://bitpay.com/currencies
 *
 * @package Bitpay
 */
class Currency extends Resource
{
    /**
     * @var array
     */
    private $availableCurrencies;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $symbol;

    /**
     * @var integer
     */
    private $precision;

    /**
     * @var string
     */
    private $exchangePctFee;

    /**
     * @var integer
     */
    private $payoutEnabled;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $plural;

    /**
     * @var array
     */
    private $alts;

    /**
     * @var string
     */
    private $minimum;

    /**
     * @var array
     */
    private $payoutFields;

    /**
     * @var string
     */
    private $codeparams;

    /**
     * Public constructor method to initialize class properties.
     *
     * @param  string    The Currency Code to use, ie USD
     * @throws Exception Throws an exception if the Currency Code is not supported
     */
    public function __construct($code = null)
    {
    	$this->availableCurrencies = array();
    	$this->code                = '';
    	$this->symbol              = '';
    	$this->precision           = 0;
    	$this->exchangePctFee      = '';
    	$this->payoutEnabled       = 0;
    	$this->name                = '';
    	$this->plural              = '';
    	$this->alts                = '';
    	$this->minimum             = '';
    	$this->payoutFields        = array();

    	$this->populateCurrencyList();

        foreach ($this->codeparams as $key => $value) {
        	foreach ($value as $x => $y) {
        		$this->availableCurrencies[] = trim($y->code);

                if (true === isset($code) && false === empty($code)) {
            		if ($code == $y->code) {
            			$this->code           = $y->code;
                		$this->symbol         = $y->symbol;
            	   		$this->precision      = $y->precision;
        	        	$this->exchangePctFee = $y->exchangePctFee;
        		   	    $this->payoutEnabled  = $y->payoutEnabled;
        		   	    $this->name           = $y->name;
        		        $this->plural         = $y->plural;
        	    	    $this->alts           = $y->alts;
        		   	    $this->minimum        = $y->minimum;
        		   	    $this->payoutFields   = $y->payoutFields;
        		    }
        		}
        	}
        }
    }

    /**
     * Populate the master list of currency codes.
     *
     * @throws \Exception
     */
    private function populateCurrencyList()
    {
    	try {
        	$this->codeparams = json_decode(file_get_contents('https://bitpay.com/currencies'));
    	} catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Returns a JSON-encoded associative array
     * representing all of the elements of data
     * in the Currency object.
     *
     * @return string
     */
    public function __toString()
    {
    	return json_encode(array(
    			                 'code'           => $this->code,
    			                 'symbol'         => $this->symbol,
    			                 'precision'      => $this->precision,
    			                 'exchangePctFee' => $this->exchangePctFee,
    			                 'payoutEnabled'  => $this->payoutEnabled,
    			                 'name'           => $this->name,
    			                 'plural'         => $this->plural,
    			                 'alts'           => $this->alts,
    			                 'minimum'        => $this->minimum,
    			                 'payoutFields'   => $this->payoutFields,
    	       ));
    }

    /**
     * This will attempt to find the $code in the
     * list of supported currency codes provided
     * by the call to BitPay and populate the
     * rest of the class properties with the
     * corresponding information.
     *
     * @param  string      $code  The Currency Code to use, ie USD.
     * @throws \Exception         Throws an exception if the Currency Code is not supported.
     * @return Currency
     */
    public function setCode($code)
    {
        if (false === isset($code) || true === empty($code)) {
            throw new \Exception('[ERROR] In Currency::setCode(): Missing or invalid $code parameter.');
        }

        $this->code = strtoupper(trim($code));
        $found = false;

        foreach ($this->codeparams as $key => $value) {
        	foreach ($value as $x => $y) {
        		if ($code == $y->code) {
        			$found = true;
        			$this->symbol         = $y->symbol;
        			$this->precision      = $y->precision;
        			$this->exchangePctFee = $y->exchangePctFee;
       				$this->payoutEnabled  = $y->payoutEnabled;
       				$this->name           = $y->name;
       				$this->plural         = $y->plural;
       				$this->alts           = $y->alts;
       				$this->minimum        = $y->minimum;
       				$this->payoutFields   = $y->payoutFields;
       			}
        	}
        }

        if ($found == true) {
            return $this;
        } else {
        	throw new \Exception('[ERROR] In Currency::setCode(): The $code parameter requested is not a valid currency code.');
        }
    }

    /**
     * Returns the currency code set.
     *
     * @return string
     */
    public function getCode()
    {
    	return $this->code;
    }

    /**
     * Returns the symbol for this currency code.
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Returns the precision for this currency code.
     *
     * @return string
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * Returns the exchangePctFee for this currency code.
     *
     * @return string
     */
    public function getExchangePctFee()
    {
        return $this->exchangePctFee;
    }

    /**
     * Returns the payoutEnabled for this currency code.
     *
     * @return string
     */
    public function isPayoutEnabled()
    {
        return (bool)$this->payoutEnabled;
    }

    /**
     * Returns the name for this currency code.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the pluralName for this currency code.
     *
     * @return string
     */
    public function getPluralName()
    {
        return $this->pluralName;
    }

    /**
     * Returns the alts for this currency code.
     *
     * @return string
     */
    public function getAlts()
    {
        return $this->alts;
    }

    /**
     * Returns the minimum payout for this currency code.
     *
     * @return string
     */
    public function getMinimum()
    {
    	return $this->minimum;
    }

    /**
     * Returns the payoutFields for this currency code.
     *
     * @return array
     */
    public function getPayoutFields()
    {
        return $this->payoutFields;
    }

}
