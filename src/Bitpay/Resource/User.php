<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Resource;

/**
 * @package Bitpay
 */
class User extends Resource
{
    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $address1;

    /**
     * @var string
     */
    private $address2;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $zip;

    /**
     * @var string
     */
    private $country;

    /**
     * @var bool
     */
    private $agreedToTOSandPP;

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
     * @return UserInterface
     */
    public function setPhone($phone)
    {
        if (!empty($phone) && is_string($phone) && ctype_print($phone)) {
            $this->phone = trim($phone);
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
     * @return UserInterface
     */
    public function setEmail($email)
    {
        if (!empty($email) && is_string($email) && ctype_print($email)) {
            $this->email = trim($email);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return UserInterface
     */
    public function setFirstName($firstName)
    {
        if (!empty($firstName) && is_string($firstName) && ctype_print($firstName)) {
            $this->firstName = trim($firstName);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return UserInterface
     */
    public function setLastName($lastName)
    {
        if (!empty($lastName) && is_string($lastName) && ctype_print($lastName)) {
            $this->lastName = trim($lastName);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAddress()
    {
        return $this->address1 . ' ' . $this->address2;
    }

    /**
     * @param array $address
     *
     * @return UserInterface
     */
    public function setAddress(array $address)
    {
        if (!empty($address) && is_array($address)) {
            $this->address1 = $address[0];
            $this->address2 = $address[1];
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
     * @param  string
     * @return User
     */
    public function setCity($city)
    {
        if (!empty($city) && is_string($city) && ctype_print($city)) {
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
     * @return UserInterface
     */
    public function setState($state)
    {
        if (!empty($state) && is_string($state) && ctype_print($state)) {
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
     * @return UserInterface
     */
    public function setZip($zip)
    {
        if (!empty($zip) && is_string($zip) && ctype_print($zip)) {
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
     * @return UserInterface
     */
    public function setCountry($country)
    {
        if (!empty($country) && is_string($country) && ctype_print($country)) {
            $this->country = trim($country);
        }

        return $this;
    }

    /**
     * @param bool $boolvalue
     *
     * @return User
     */
    public function setAgreedToTOSandPP($boolvalue)
    {
        if (!empty($boolvalue)) {
            $this->agreedToTOSandPP = $boolvalue;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getAgreedToTOSandPP()
    {
        return $this->agreedToTOSandPP;
    }
}
