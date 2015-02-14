<?php
/**
 * PHP Client Library for the new cryptographically secure BitPay API.
 *
 * @copyright  Copyright 2011-2014 BitPay, Inc.
 * @author     Integrations Development Team <integrations@bitpay.com>
 * @license    https://raw.githubusercontent.com/bitpay/php-bitpay-client/master/LICENSE The MIT License (MIT)
 * @see        https://github.com/bitpay/php-bitpay-client
 * @package    Bitpay
 * @since      2.0.0
 * @version    2.2.2
 * @filesource
 */

namespace Bitpay\Resource;

use Bitpay\Resource\Currency;
use Bitpay\Resource\Item;

/**
 * Class representing a BitPay Invoice. Invoices are time-sensitive payment requests addressed
 * to specific buyers. An invoice has a fixed price, typically denominated in fiat currency.
 * It also has a BTC equivalent price, calculated by BitPay, with an expiration time of about
 * 15 minutes.
 *
 * @package Bitpay
 */
class Invoice extends Resource
{
    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var string
     */
    private $orderId;

    /**
     * @var string
     */
    private $transactionSpeed;

    /**
     * @var string
     */
    private $redirectUrl;

    /**
     * @var string
     */
    private $posData;

    /**
     * @var boolean
     */
    private $fullNotifications;

    /**
     * @var string
     */
    private $url;

    /**
     * @var float
     */
    private $btcPrice;

    /**
     * @var \DateTime
     */
    private $invoiceTime;

    /**
     * @var \DateTime
     */
    private $expirationTime;

    /**
     * @var float
     */
    private $btcPaid;

    /**
     * @var float
     */
    private $rate;

    /**
     * Public constructor method to initialize
     * class properties.
     *
     * @param Currency $currency
     * @param string   $orderId
     * @param Buyer    $buyer
     * @param Item     $item
     * @param string   $transactionSpeed
     * @param string   $notificationEmail
     * @param string   $notificationUrl
     * @param string   $redirectUrl
     * @param string   $posData
     * @param bool     $fullNotifications
     */
    public function __construct(Currency $currency = null, $orderId = null, Buyer $buyer = null, Item $item = null, $transactionSpeed = null, $notificationEmail = null, $notificationUrl = null, $redirectUrl = null, $posData = null, $fullNotifications = null)
    {
    	if (true === isset($currency) && false === empty($currency) && true === is_object($currency)) {
    		$this->currency = $currency;
    	} else {
    		$this->currency = new Currency('BTC');
    	}

    	if (true === isset($orderId) && false === empty($orderId) && true === is_string($orderId)) {
    		$this->orderId = trim($orderId);
    	} else {
    		$this->orderId = microtime();
    	}

    	if (true === isset($buyer) && false === empty($buyer) && true === is_object($buyer)) {
    		$this->buyer = $buyer;
    	} else {
    		$this->buyer = new Buyer();
    	}

    	if (true === isset($item) && false === empty($item) && true === is_object($item)) {
    		$this->buyer = $item;
    	} else {
    		$this->buyer = new Item();
    	}

    	if (true === isset($transactionSpeed) && false === empty($transactionSpeed) && true === is_string($transactionSpeed)) {
    		switch (substr(strtolower(trim($transactionSpeed)),0,3)) {
    			case 'hig':
    				$this->transactionSpeed  = self::TRANSACTION_SPEED_HIGH;
    				break;
    			case 'med':
    				$this->transactionSpeed  = self::TRANSACTION_SPEED_MEDIUM;
    				break;
    			case 'low':
    			default:
    				$this->transactionSpeed  = self::TRANSACTION_SPEED_LOW;
    		}
    	} else {
    		$this->transactionSpeed  = self::TRANSACTION_SPEED_LOW;
    	}

    	if (true === isset($notificationEmail) && false === empty($notificationEmail) && true === is_string($notificationEmail)) {
    		$this->notificationEmail = trim($notificationEmail);
    	} else {
    		$this->notificationEmail = '';
    	}

    	if (true === isset($notificationUrl) && false === empty($notificationUrl) && true === is_string($notificationUrl)) {
    		$this->notificationUrl = trim($notificationUrl);
    	} else {
    		$this->notificationUrl = '';
    	}

    	if (true === isset($redirectUrl) && false === empty($redirectUrl) && true === is_string($redirectUrl)) {
    		$this->redirectUrl = trim($redirectUrl);
    	} else {
    		$this->redirectUrl = '';
    	}

    	if (true === isset($posData) && false === empty($posData) && true === is_string($posData)) {
    		$this->buyer = substr(trim($posData),0,99);
    	} else {
    		$this->buyer = '';
    	}

    	if (true === isset($fullNotifications) && false === empty($fullNotifications)) {
    		$this->fullNotifications = (bool) $fullNotifications;
    	} else {
    		$this->fullNotifications = false;
    	}
    }

    /**
     * This is the amount that is required to be collected from the buyer. Note, if this is
     * specified in a currency other than BTC, the price will be converted into BTC at
     * market exchange rates to determine the amount collected from the buyer.
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->getItem()->getPrice();
    }

    /**
     * Sets the price for the main item.
     *
     * @param  float
     * @return Invoice
     */
    public function setPrice($price)
    {
        if (true === isset($price) && false === empty($price)) {
            $this->getItem()->setPrice($price);
        }

        return $this;
    }

    /**
     * Returns the transaction speed string for this invoice.
     *
     * @return string
     */
    public function getTransactionSpeed()
    {
        return $this->transactionSpeed;
    }

    /**
     * Sets the transaction speed string for this invoice.
     *
     * @param  string
     * @return Invoice
     */
    public function setTransactionSpeed($transactionSpeed)
    {
        if (true === isset($transactionSpeed) && false === empty($transactionSpeed) && true === is_string($transactionSpeed)) {
    		switch (substr(strtolower(trim($transactionSpeed)),0,3)) {
    			case 'hig':
    				$this->transactionSpeed = self::TRANSACTION_SPEED_HIGH;
    				break;
    			case 'med':
    				$this->transactionSpeed = self::TRANSACTION_SPEED_MEDIUM;
    				break;
    			case 'low':
    			default:
    				$this->transactionSpeed = self::TRANSACTION_SPEED_LOW;
    		}
    	} else {
    		$this->transactionSpeed = self::TRANSACTION_SPEED_LOW;
    	}

        return $this;
    }

    /**
     * Returns the redirectUrl string for this invoice.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Sets the redirectUrl string for this invoice.
     *
     * @param  string
     * @return Invoice
     */
    public function setRedirectUrl($redirectUrl)
    {
        if (true === isset($redirectUrl) && false === empty($redirectUrl) && true === is_string($redirectUrl)) {
            $this->redirectUrl = trim($redirectUrl);
        }

        return $this;
    }

    /**
     * Returns the posData string for this invoice.
     *
     * @return string
     */
    public function getPosData()
    {
        return $this->posData;
    }

    /**
     * Sets the posData string for this invoice.
     * Limited to 100 characters for this param.
     *
     * @param  string
     * @return Invoice
     */
    public function setPosData($posData)
    {
        if (true === isset($posData) && false === empty($posData) && true === is_string($posData)) {
            $this->posData = substr(trim($posData),0,100);
        }

        return $this;
    }

    /**
     * Returns the boolean value of the fullNotifications parameter.
     *
     * @return bool
     */
    public function isFullNotifications()
    {
        return (bool)$this->fullNotifications;
    }

    /**
     * Sets the boolean value of the fullNotifications parameter.
     *
     * @param  string
     * @return Invoice
     */
    public function setFullNotifications($notifications)
    {
    	if (true === isset($notifications) && false === empty($notifications)) {
            $this->fullNotifications = (boolean) $notifications;
    	}

        return $this;
    }

    /**
     * Returns the invoice url string for this invoice.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the invoice url string for this invoice.
     *
     * @param  string
     * @return Invoice
     */
    public function setUrl($url)
    {
        if (true === isset($url) && false === empty($url) && true === is_string($url)) {
            $this->url = trim($url);
        }

        return $this;
    }

    /**
     * Returns the btcPrice value for this invoice.
     *
     * @return mixed
     */
    public function getBtcPrice()
    {
        return $this->btcPrice;
    }

    /**
     * Sets the btcPrice value for this invoice.
     *
     * @param  float
     * @return Invoice
     */
    public function setBtcPrice($btcPrice)
    {
        if (true === isset($btcPrice) && false === empty($btcPrice)) {
            $this->btcPrice = (float)$btcPrice;
        }

        return $this;
    }

    /**
     * Returns the invoiceTime for this invoice.
     *
     * @return \DateTime
     */
    public function getInvoiceTime()
    {
        return $this->invoiceTime;
    }

    /**
     * Sets the invoiceTime for this invoice.
     *
     * @param  \DateTime
     * @return Invoice
     */
    public function setInvoiceTime(\DateTime $invoiceTime)
    {
        if (true === isset($invoiceTime) && false === empty($invoiceTime) && is_object($invoiceTime)) {
            $this->invoiceTime = $invoiceTime;
        }

        return $this;
    }

    /**
     * Returns the expirationTime for this invoice.
     *
     * @return \DateTime
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    /**
     * Sets the expirationTime for this invoice.
     *
     * @param  \DateTime
     * @return Invoice
     */
    public function setExpirationTime(\DateTime $expirationTime)
    {
        if (true === isset($expirationTime) && false === empty($expirationTime) && is_object($expirationTime)) {
            $this->expirationTime = $expirationTime;
        }

        return $this;
    }

    /**
     * Returns the orderId string for this invoice.
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Sets the orderId string for this invoice.
     *
     * @param  string
     * @return Invoice
     */
    public function setOrderId($orderId)
    {
        if (true === isset($orderId) && false === empty($orderId) && is_string($orderId)) {
            $this->orderId = trim($orderId);
        }

        return $this;
    }

    /**
     * Returns the Item $physical boolean for this invoice.
     *
     * @return bool
     */
    public function isPhysical()
    {
        return $this->getItem()->isPhysical();
    }

    /**
     * Returns the btcPaid amount string for this invoice.
     *
     * @return string
     */
    public function getBtcPaid()
    {
        return $this->btcPaid;
    }

    /**
     * Sets the btcPaid amount string for this invoice.
     *
     * @param  string
     * @return Invoice
     */
    public function setBtcPaid($btcPaid)
    {
        if (true === isset($btcPaid) && false === empty($btcPaid)) {
            $this->btcPaid = trim($btcPaid);
        }

        return $this;
    }

    /**
     * Returns the btc rate string for this invoice.
     *
     * @return string
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Sets the btc rate string for this invoice.
     *
     * @param  string
     * @return Invoice
     */
    public function setRate($rate)
    {
        if (true === isset($rate) && false === empty($rate)) {
            $this->rate = trim($rate);
        }

        return $this;
    }
}
