<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Storage;

use Bitpay\Key;

/**
 * Interface for all storage engines.
 *
 * @package Bitpay
 */
interface Storage
{
    /**
     * @param Key $key
     */
    public function persist(Key $key);

    /**
     * Retrieve a Key object from storage.
     *
     * @param string $id
     * @return KeyInterface
     */
    public function load($id);
}
