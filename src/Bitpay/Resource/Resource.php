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

/**
 * Abstract class inherited by the various Bitpay resource classes
 *
 * @package Bitpay
 */
abstract class Resource
{
	const STATUS_NEW               = 'new';
	const STATUS_FUNDED            = 'funded';
	const STATUS_PROCESSING        = 'processing';
	const STATUS_COMPLETE          = 'complete';
	const STATUS_CANCELLED         = 'cancelled';
	const STATUS_PAID              = 'paid';
	const STATUS_CONFIRMED         = 'confirmed';
	const STATUS_EXPIRED           = 'expired';
	const STATUS_INVALID           = 'invalid';
	const TRANSACTION_SPEED_HIGH   = 'high';
	const TRANSACTION_SPEED_MEDIUM = 'medium';
	const TRANSACTION_SPEED_LOW    = 'low';

	/**
	 * @var string
	 */
	private $id;

	/**
	 * Facade needed to access this resource.
	 *
	 * @var string
	 */
	private $facade;

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var Token
	 */
	private $token;

	/**
	 * @var Currency
	 */
	private $currency;

	/**
	 * @var Buyer
	 */
	private $buyer;

	/**
	 * @var Item
	 */
	private $item;

	/**
	 * @var string
	 */
	private $rate;

	/**
	 * @var float
	 */
	private $amount;

	/**
	 * @var float
	 */
	protected $btc;

	/**
	 * @var string
	 */
	private $notificationEmail;

	/**
	 * @var string
	 */
	private $notificationUrl;

	/**
	 * Supported REST verbs for this resource.
	 *
	 * @var array
	 */
	private $supportedMethods;

	/**
	 * @var \DateTime
	 */
	private $currentTime;

	/**
	 * @var bool
	 */
	private $exceptionStatus;

	/**
	 * Returns the id for this resource.
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set the batch ID as assigned from bitpay.
	 *
	 * @param  string
	 * @return mixed
	 */
	public function setId($id) {
		$this->id = $id;

		return $this;
	}

	/**
	 * Returns the facade parameter for this resource.
	 * This will indicate the type of token required
	 * for access.
	 *
	 * @return string
	 */
	public function getFacade() {
		return $this->facade;
	}

	/**
	 * Set the facade parameter for this resource.
	 *
	 * @param  Facade
	 * @return mixed
	 */
	public function setFacade($facade) {
		$this->facade = $facade;

		return $this;
	}

	/**
	 * Returns the status string for this resource.
	 *
	 * @return string
	 */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the status string for this resource.
     *
     * @param  string
     * @return Invoice
     */
    public function setStatus($status)
    {
        if (true === isset($status) && false === empty($status) && true === is_string($status)) {
            $this->status = trim($status);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getToken()
    {
    	return $this->token;
    }

    /**
     * Set the token to authorize this request.
     *
     * @param  Token
     * @return $this
     */
    public function setToken(Token $token)
    {
    	if (true === isset($token) && false === empty($token)) {
    		$this->token = $token;
    	}
    	return $this;
    }

    /**
     * Gets the Buyer object for this transaction resource.
     *
     * @return Buyer
     */
    public function getBuyer()
    {
    	return $this->buyer;
    }

    /**
     * Sets the Buyer object for this transaction resource.
     *
     * @param  Buyer
     * @return $this
     */
    public function setBuyer(Buyer $buyer)
    {
    	if (true === isset($buyer) && false === empty($buyer)) {
    		$this->buyer = $buyer;
    	}

    	return $this;
    }

    /**
     * Returns the Item object used for this invoice.
     *
     * @return Item
     */
    public function getItem()
    {
    	// If there is not an item already set, we create one
    	// so that some methods do not throw errors about methods and
    	// non-objects.
    	if (false === isset($this->item) || true === empty($this->item)) {
    		$this->item = new Item();
    	}

    	return $this->item;
    }

    /**
     * Sets the Item object for this invoice.
     *
     * @param  Item
     * @return Invoice
     */
    public function setItem(Item $item)
    {
    	if (true === isset($item) && false === empty($item) && true === is_object($item)) {
    		$this->item = $item;
    	}

    	return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNotificationEmail()
    {
    	return $this->notificationEmail;
    }

    /**
     * Set an email address where updates to payout status should be sent.
     *
     * @param $notificationEmail
     * @return $this
     */
    public function setNotificationEmail($notificationEmail)
    {
    	if (true === isset($notificationEmail) && false === empty($notificationEmail)) {
    		$this->notificationEmail = trim($notificationEmail);
    	}

    	return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNotificationUrl()
    {
    	return $this->notificationUrl;
    }

    /**
     * Set a notification url - where updated Payout objects will be sent
     *
     * @param $notificationUrl
     * @return $this
     */
    public function setNotificationUrl($notificationUrl)
    {
    	if (true === isset($notificationUrl) && false === empty($notificationUrl)) {
    		$this->notificationUrl = trim($notificationUrl);
    	}

    	return $this;
    }

    /**
     * Returns the currency string used for this bill. If
     * returnObject is true, it will return the entire
     * Currency object. Otherwise, it will return just
     * the currency code string.
     *
     * @param  bool
     * @return Currency|string
     */
    public function getCurrency($returnObject = false)
    {
    	if ($returnObject == true) {
    		return $this->currency;
    	} else {
    		return $this->currency->getCode();
    	}
    }

    /**
     * Sets the currency used for this bill.
     *
     * @param  Currency
     * @return Bill
     */
    public function setCurrency(Currency $currency)
    {
    	if (true === isset($currency) && false === empty($currency) && true === is_object($currency)) {
    		$this->currency = $currency;
    	}

    	return $this;
    }

    /**
     * Returns the currentTime for this invoice.
     *
     * @return \DateTime
     */
    public function getCurrentTime()
    {
    	return $this->currentTime;
    }

    /**
     * Sets the currentTime for this invoice.
     *
     * @param  \DateTime
     * @return Invoice
     */
    public function setCurrentTime(\DateTime $currentTime)
    {
    	if (true === isset($currentTime) && false === empty($currentTime) && is_object($currentTime)) {
    		$this->currentTime = $currentTime;
    	}

    	return $this;
    }

    /**
     * Returns the exception status string for this invoice.
     *
     * @return string
     */
    public function getExceptionStatus()
    {
    	return $this->exceptionStatus;
    }

    /**
     * Sets the exception status string for this invoice.
     *
     * @param  string
     * @return Invoice
     */
    public function setExceptionStatus($exceptionStatus)
    {
    	if (true === isset($exceptionStatus) && true === is_string($exceptionStatus)) {
    		$this->exceptionStatus = trim($exceptionStatus);
    	}

    	return $this;
    }

    /**
     * Get rate assigned to payout at effectiveDate
     */
    public function getRate()
    {
    	return $this->rate;
    }

    /**
     * Set the rate in bitcoin for the payouts of this transaction.
     * @param $rate
     * @return $this
     */
    public function setRate($rate)
    {
    	if (true === isset($rate) && false === empty($rate)) {
    		$this->rate = $rate;
    	}

    	return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
    	return $this->amount;
    }

    /**
     * Sets the amount for this payout.
     * @param $amount
     * @return $this
     */
    public function setAmount($amount)
    {
    	if (true === isset($amount) && false === empty($amount)) {
    		$this->amount = $amount;
    	}

    	return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBtcAmount()
    {
    	return $this->btc;
    }

    /**
     * Set the Bitcoin amount for this payout, once set by Bitpay.
     * @param $amount
     * @return $this
     */
    public function setBtcAmount($amount)
    {
    	if (!empty($amount)) {
    		$this->btc = $amount;
    	}

    	return $this;
    }
}