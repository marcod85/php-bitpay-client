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
 * Class representing the public key resource used in message signing.
 */
class PublicKey extends Key
{
    /**
     * Returns the compressed public key value
     *
     * @return string
     */
    public function __toString()
    {
        if (true == is_null($this->x)) {
            return '';
        }

        if (Math::mod('0x'.$this->y, '0x02') == '1') {
            return sprintf('03%s', $this->x);
        } else {
            return sprintf('02%s', $this->x);
        }
    }

    /**
     * @param  PrivateKey
     * @return PublicKey
     */
    public static function createFromPrivateKey(PrivateKey $private)
    {
        $public = new self();
        $public->setPrivateKey($private);

        return $public;
    }

    /**
     * Generates an uncompressed and compressed EC public key.
     *
     * @param  PrivateKey
     * @return PublicKey
     * @throws \Exception
     */
    public function generate(PrivateKey $privateKey = null)
    {
        if ($privateKey instanceof PrivateKey) {
            $this->setPrivateKey($privateKey);
        }

        if (!empty($this->hex)) {
            return $this;
        }

        if (is_null($this->privateKey)) {
            throw new \Exception('Please `setPrivateKey` before you generate a public key');
        }

        if (!$this->privateKey->isGenerated()) {
            $this->privateKey->generate();
        }

        if (!$this->privateKey->isValid()) {
            throw new \Exception('Private Key is invalid and cannot be used to generate a public key');
        }

        $point = new Point(
            '0x'.substr(self::G, 2, 64),
            '0x'.substr(self::G, 66, 64)
        );

        $R = Point::doubleAndAdd(
            '0x'.$this->privateKey->getHex(),
            $point
        );

        $RxHex = Base64::encode($R->getX());
        $RyHex = Base64::encode($R->getY());

        $RxHex = str_pad($RxHex, 64, '0', STR_PAD_LEFT);
        $RyHex = str_pad($RyHex, 64, '0', STR_PAD_LEFT);

        $this->x   = $RxHex;
        $this->y   = $RyHex;

        $this->hex = sprintf('%s%s', $RxHex, $RyHex);
        $this->dec = Base64::decode($this->hex);

        return $this;
    }
}
