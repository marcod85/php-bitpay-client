<?php
/**
 * PHP Client Library for the new cryptographically secure BitPay API.
 *
 * @copyright  Copyright 2011-2014 BitPay, Inc.
 * @author     Integrations Development Team <integrations@bitpay.com>
 * @license    https://raw.githubusercontent.com/bitpay/php-bitpay-client/master/LICENSE The MIT License (MIT)
 * @link       https://github.com/bitpay/php-bitpay-client
 * @package    Bitpay
 * @since      2.0.0
 * @version    2.3.0
 * @filesource
 */

namespace Bitpay\Crypto;

use Bitpay\Crypto\Math\Math;
use Bitpay\Crypto\Math\Base64;

/**
 * Private key resource used in keypair generation and message signing.
 */
class PrivateKey extends Key
{
    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->hex;
    }

    /**
     * Generates an EC private key
     *
     * @return PrivateKey
     */
    public function generate()
    {
        if (true === isset($this->hex) && false === empty($this->hex)) {
            return $this;
        }

        do {
            $privateKey = self::generateRandom(32);
            $this->hex  = strtolower(bin2hex($privateKey));
        } while (Math::cmp('0x' . $this->hex, '1') <= 0 || Math::cmp('0x' . $this->hex, '0x' . self::N) >= 0);

        $this->dec = Base64::decode($this->hex);

        if (Math::cmp($this->dec, '0') <= 0) {
            throw new \Exception('[ERROR] In PrivateKey::generate(): Error decoding hex value. The decimal value returned was <= 0.');
        }

        return $this;
    }
}
