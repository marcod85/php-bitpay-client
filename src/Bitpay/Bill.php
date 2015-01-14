<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * Represents a bill object for the calling merchant.
 * see https://bitpay.com/api#resource-Bills
 *
 * @package Bitpay
 */
class Bill implements BillInterface
{
    /**
     * @var array
     */
    protected $items;

    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $address;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $zip;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $showRate;

    /**
     * @var boolean
     */
    protected $archived;

    /**
     */
    public function __construct()
    {
        $this->address  = array();
        $this->archived = false;
        $this->currency = new Currency();
        $this->items    = array();
    }

    /**
     * Returns an associative array representing all
     * of the pieces of data in the Bill object.
     *
     * @return array
     */
    public function __toString()
    {
        return array(
                'items'    => $items,
                'currency' => $currency,
                'name'     => $name,
                'address'  => $address,
                'city'     => $city,
                'state'    => $state,
                'zip'      => $zip,
                'country'  => $country,
                'email'    => $email,
                'phone'    => $phone,
                'status'   => $status,
                'showRate' => $showRate,
                'archived' => $archived,
            );
    }

    /**
     * @inheritdoc
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param ItemInterface $item
     *
     * @return BillInterface
     */
    public function addItem(ItemInterface $item)
    {
        if (true === isset($item) && false === empty($item)) {
            $this->items[] = $item;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param CurrencyInterface $currency
     *
     * @return BillInterface
     */
    public function setCurrency(CurrencyInterface $currency)
    {
        if (true === isset($currency) && false === empty($currency)) {
            $this->currency = $currency;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return BillInterface
     */
    public function setName($name)
    {
        if (true === isset($name) && false === empty($name) && true === ctype_print($name)) {
            $this->name = trim($name);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param array $address
     *
     * @return BillInterface
     */
    public function setAddress($address)
    {
        if (true === isset($address) && false === empty($address) && true === is_array($address)) {
            $this->address = $address;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     *
     * @return BillInterface
     */
    public function setCity($city)
    {
        if (true === isset($city) && false === empty($city) && true === ctype_print($city)) {
            $this->city = trim($city);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return BillInterface
     */
    public function setState($state)
    {
        if (true === isset($state) && false === empty($state) && true === ctype_print($state)) {
            $this->state = trim($state);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     *
     * @return BillInterface
     */
    public function setZip($zip)
    {
        if (true === isset($zip) && false === empty($zip) && true === ctype_print($zip)) {
            $this->zip = trim($zip);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     *
     * @return BillInterface
     */
    public function setCountry($country)
    {
        if (true === isset($country) && false === empty($country) && true === ctype_print($country)) {
            $this->country = trim($country);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return BillInterface
     */
    public function setEmail($email)
    {
        if (true === isset($email) && false === empty($email) && true === ctype_print($email)) {
            $this->email = trim($email);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return BillInterface
     */
    public function setPhone($phone)
    {
        if (true === isset($phone) && false === empty($phone) && true === ctype_print($phone)) {
            $this->phone = trim($phone);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return BillInterface
     */
    public function setStatus($status)
    {
        if (true === isset($status) && false === empty($status) && true === ctype_print($status)) {
            $this->status = trim($status);
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
     * @return BillInterface
     */
    public function setShowRate($showRate)
    {
        if (true === isset($showRate) && false === empty($showRate) && true === ctype_print($showRate)) {
            $this->showRate = trim($showRate);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArchived()
    {
        return $this->archived;
    }

    /**
     * @param boolean $archived
     *
     * @return BillInterface
     */
    public function setArchived($archived)
    {
        $this->archived = (boolean) $archived;

        return $this;
    }
}
