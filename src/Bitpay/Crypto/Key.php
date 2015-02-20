<?php
/**
 * PHP Client Library for the new cryptographically secure BitPay API.
 *
 * @copyright  Copyright 2011-2015 BitPay, Inc.
 * @author     Integrations Development Team <integrations@bitpay.com>
 * @license    https://raw.githubusercontent.com/bitpay/php-bitpay-client/master/LICENSE The MIT License (MIT)
 * @link       https://github.com/bitpay/php-bitpay-client
 * @package    Bitpay
 * @since      2.0.0
 * @version    2.3.0
 * @filesource
 */

namespace Bitpay\Crypto;

/**
 * Abstract object that is used for Public, Private, and SIN keys.
 */
abstract class Key extends Crypto implements \Serializable
{
    /**
     * Public constructor method to initialize class properties.
     *
     * @param string $id
     */
    public function __construct($id = null)
    {
    	parent::__construct();

        $this->id = $id;
    }

    /**
     * Returns a new instance of self.
     *
     * @param  string $id
     * @return Key
     */
    public function create($id = null)
    {
        $class = get_called_class();

        return new $class($id);
    }

    /**
     * Returns a storable representation of this object.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(
            array(
                $this->id,
                $this->x,
                $this->y,
                $this->hex,
                $this->dec,
            )
        );
    }

    /**
     * Takes a single serialized variable and converts it back into a PHP value.
     *
     * @param string
     */
    public function unserialize($data)
    {
        list(
            $this->id,
            $this->x,
            $this->y,
            $this->hex,
            $this->dec
        ) = unserialize($data);
    }
}
