<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Resource;

/**
 * This class represents a resource access token.
 * see https://bitpay.com/api#resource-Tokens
 *
 * @package Bitpay
 */
class Token extends Resource
{
    /**
     * @var string
     */
    protected $resource;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected $pairingCode;

    /**
     * @var \DateTime
     */
    protected $pairingExpiration;

    /**
     * Public constructor method to initialize
     * class properties.
     *
     * @param array $token      An array of User objects
     * @param array $resource   An array of Org objects
     * @param array $facade     An array of Org objects
     * @param array $resource   An array of Org objects
     * @param array $resource   An array of Org objects
     */
    public function __construct($token = null, $resource = null, $facade = null, $createdAt = null, $policies = null, $pairingCode = null)
    {
    	$this->token       = '';
    	$this->resource    = '';
    	$this->facade      = '';
    	$this->createdAt   = null;
        $this->policies    = array();
        $this->pairingCode = '';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getToken();
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return array
     */
    public function getPolicies()
    {
        return $this->policies;
    }

    public function setPolicies($policies)
    {
        $this->policies = $policies;

        return $this;
    }

    /**
     * @return string
     */
    public function getPairingCode()
    {
        return $this->pairingCode;
    }

    public function setPairingCode($pairingCode)
    {
        $this->pairingCode = $pairingCode;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPairingExpiration()
    {
        return $this->pairingExpiration;
    }

    public function setPairingExpiration(\DateTime $pairingExpiration)
    {
        $this->pairingExpiration = $pairingExpiration;

        return $this;
    }
}
