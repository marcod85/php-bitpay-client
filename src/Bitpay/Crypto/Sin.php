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

use Bitpay\Crypto\Hash;
use Bitpay\Crypto\Math\Base58;

/**
 * Class that represents a Service Identification Number (SIN), see:
 * https://en.bitcoin.it/wiki/Identity_protocol_v1
 */
class Sin extends Key
{
    /**
     * @var string
     */
    private $value;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * Generates the SIN
     *
     * @return Sin
     * @throws \Exception
     */
    public function generate()
    {
        if (true === empty($this->publicKey)) {
            throw new \Exception('Public Key has not been set');
        }

        $compressedValue = $this->publicKey;

        if (empty($compressedValue)) {
            throw new \Exception('The Public Key needs to be generated.');
        }

        $step1 = Hash::sha256(Base10::binConv($compressedValue), true);

        $step2 = Hash::ripe160($step1);

        $step3 = sprintf(
            '%s%s%s',
            self::SIN_VERSION,
            self::SIN_TYPE,
            $step2
        );

        $step4 = Hash::twoSha256(Base10::binConv($step3), true);

        $step5 = substr(bin2hex($step4), 0, 8);

        $step6 = $step3 . $step5;

        $this->value = Base58::encode($step6);

        return $this;
    }
}
