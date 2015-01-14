<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * @package Bitpay
 */
class AccessToken implements AccessTokenInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var boolean
     */
    protected $useNonce;

    /**
     * Public constructor to initialize nonce usage parameter.
     */
    public function __construct()
    {
        /**
         * Set various defaults for this object.
         */
        $this->useNonce = true;
    }

    /**
     * @param string $id
     *
     * @return AccessTokenInterface
     */
    public function setId($id)
    {
        if (true  === isset($id)     &&
            false === empty($id)     &&
            true  === is_string($id))
        {
            $this->id = trim($id);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $email
     *
     * @return AccessTokenInterface
     */
    public function setEmail($email)
    {
        if (true  === isset($email)     &&
            false === empty($email)     &&
            true  === is_string($email))
        {
            $this->email = trim($email);
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
     * @param string $label
     *
     * @return AccessTokenInterface
     */
    public function setLabel($label)
    {
        if (true  === isset($label)     &&
            false === empty($label)     &&
            true  === is_string($label))
        {
            $this->label = trim($label);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @inheritdoc
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @inheritdoc
     */
    public function isNonceDisabled()
    {
        return !($this->useNonce);
    }

    /**
     * Enable nonce usage
     *
     * @return AccessTokenInterface
     */
    public function nonceEnable()
    {
        $this->useNonce = true;

        return $this;
    }

    /**
     * Disable nonce usage
     *
     * @return AccessTokenInterface
     */
    public function nonceDisable()
    {
        $this->useNonce = false;

        return $this;
    }
}
