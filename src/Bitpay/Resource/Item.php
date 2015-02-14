<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Resource;

/**
 * Class representing a line item on a bill or invoice.
 *
 * @package Bitpay
 */
class Item extends Resource
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $description;

    /**
     * @var float
     */
    private $price;

    /**
     * @var integer
     */
    private $quantity;

    /**
     * @var boolean
     */
    private $physical;

    /**
     */
    public function __construct()
    {
        $this->physical = false;
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return ItemInterface
     */
    public function setCode($code)
    {
        if (true === isset($code) && false === empty($code)) {
            $this->code = $code;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return ItemInterface
     */
    public function setDescription($description)
    {
        if (true === isset($description) && false === empty($description)) {
            $this->description = $description;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     *
     * @return ItemInterface
     */
    public function setPrice($price)
    {
        if (true === isset($price) && 1 !== preg_match('/^[0-9]+(?:\.[0-9]{1,2})?$/', $price)) {
            throw new \Exception("[ERROR] In Item::Price must be formatted as a float");
        }
        $this->price = $price;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param integer $quantity
     *
     * @return ItemInterface
     */
    public function setQuantity($quantity)
    {
        if (true === isset($quantity) && false === empty($quantity)) {
            $this->quantity = $quantity;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isPhysical()
    {
        return $this->physical;
    }

    /**
     * @param boolean $physical
     *
     * @return ItemInterface
     */
    public function setPhysical($physical)
    {
        if (true === isset($physical) && true === is_bool($physical)) {
            $this->physical = (boolean) $physical;
        }

        return $this;
    }
}
