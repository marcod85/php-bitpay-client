<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Resource;

use Bitpay\Resource\Item;
use Bitpay\Resource\Currency;
use Bitpay\Resource\Buyer;
use Bitpay\Resource\Resource;

/**
 * Represents a bill object for the calling merchant.
 * see https://bitpay.com/api#resource-Bills
 *
 * @package Bitpay
 */
class Bill extends Resource
{
    /**
     * @var array
     */
    private $items;

    /**
     * @var string
     */
    private $showRate;

    /**
     * @var string
     */
    private $archived;

    /**
     * UTC date, ISO-8601 format yyyy-mm-dd or
     * yyyy-mm-ddThh:mm:ssZ. Default is current
     * time.
     *
     * @var string
     */
    private $dueDate;

    /**
     * Public constructor method to initialize
     * class properties.
     *
     * @param array    $items     An array of Item objects for the bill
     * @param Currency $currency  The currency object for payment
     * @param string   $showRate  Whether or not to display the rate
     * @param string   $archived  Is this bill archived or not
     * @param string   $name      Customer's name who is getting the bill
     * @param string   $address1  Customer's address, first line
     * @param string   $address2  Customer's address, second line
     * @param string   $city      Customer's city
     * @param string   $state     Customer's state
     * @param string   $zip       Customer's zip/postal code
     * @param string   $country   Customer's country code
     * @param string   $email     Customer's email address
     * @param string   $phone     Customer's phone number
     * @param string   $dueDate   The due date for this bill
     */
    public function __construct($items = null, Currency $currency = null, Buyer $buyer = null, $dueDate = null, $showRate = null, $archived = null)
    {
    	if (true === isset($items) && false === empty($items) && true === is_array($items)) {
    		$this->items = $items;
    	} else {
    		$this->items = array(new Item());
    	}

    	if (true === isset($currency) && false === empty($currency) && true === is_object($currency)) {
    		$this->currency = $currency;
    	} else {
    		$this->currency = new Currency('BTC');
    	}

    	if (true === isset($buyer) && false === empty($buyer) && true === is_object($buyer)) {
    		$this->buyer = $buyer;
    	} else {
    		$this->buyer = new Buyer;
    	}

        if (true === isset($showRate) && false === empty($showRate) && true === is_string($showRate)) {
        	$this->showRate = $showRate;
        } else {
        	$this->showRate = '';
        }

        if (true === isset($archived) && false === empty($archived) && true === is_string($archived)) {
        	$this->archived = $archived;
        } else {
        	$this->archived = '';
        }

        if (true === isset($dueDate) && false === empty($dueDate) && true === is_string($dueDate)) {
        	$this->dueDate = $dueDate;
        } else {
        	$this->dueDate = '';
        }
    }

    /**
     * Returns a JSON-encoded associative array
     * representing all of the elements of data
     * in the Bill object.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(array(
                'items'    => array($this->items),
                'currency' => $this->currency->getCode(),
        		'showRate' => $this->showRate,
        		'archived' => $this->archived,
                'name'     => $this->buyer->getFirstName() . ' ' . $this->buyer->getLastName(),
                'address1' => $this->buyer->getAddress(),
                'city'     => $this->buyer->getCity(),
                'state'    => $this->buyer->getState(),
                'zip'      => $this->buyer->getZip(),
                'country'  => $this->buyer->getCountry(),
                'email'    => $this->buyer->getEmail(),
                'phone'    => $this->buyer->getPhone(),
        		'dueDate'  => $this->dueDate,
            ));
    }

    /**
     * Return the array of items on the bill
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Sets the complete array of items on
     * the bill in one function call.
     *
     * @param  array
     * @return Bill
     */
    public function setItems($items = array())
    {
    	if (true === isset($items) && false === empty($items) && true === is_array($items)) {
    		$this->items = $items;
    	}

    	return $this;
    }

    /**
     * Adds a single item to the items array.
     *
     * @param  Item
     * @return Bill
     */
    public function addItem(Item $item)
    {
    	if (true === isset($item) && false === empty($item)) {
    		$this->items[] = $item;
    	}

    	return $this;
    }

    /**
     * Return the archival status of the bill.
     *
     * @return bool
     */
    public function getArchived()
    {
    	return (bool)$this->archived;
    }

    /**
     * Sets the archival status of the bill.
     *
     * @param  bool
     * @return Bill
     */
    public function setArchived($archived = false)
    {
    	if (true === isset($archived) && false === empty($archived) && true === is_bool($archived)) {
    		$this->archived = $archived;
    	}

    	return $this;
    }

    /**
     * @inheritdoc
     */
    public function getShowRate()
    {
    	return $this->showRate;
    }

    /**
     * @param string $showRate
     *
     * @return Bill
     */
    public function setShowRate($showRate)
    {
    	if (true === isset($showRate) && false === empty($showRate) && true === ctype_print($showRate)) {
    		$this->showRate = trim($showRate);
    	}

    	return $this;
    }

    /**
     * Returns the due date value for this bill.
     *
     * @return string
     */
    public function getDueDate()
    {
    	return $this->dueDate;
    }

    /**
     * Sets the due date value for this bill.
     *
     * @param  string
     * @return Bill
     */
    public function setDueDate($date)
    {
    	if (true === isset($date) && false === empty($date)) {
    		$this->dueDate = trim($date);
    	}

    	return $this;
    }
}
