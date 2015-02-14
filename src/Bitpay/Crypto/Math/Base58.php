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

namespace Bitpay\Crypto\Math;

/**
 * Utility class for encoding/decoding BASE-58 data.
 */
final class Base58 extends Math
{
    /**
     * Encodes hex $data into BASE-58 format.
     *
     * @param  string $data
     * @return string
     */
    public static function encode($data)
    {
    	$this->argCheck($data);

        $x = Base64::decode($data);

        $output_string = '';

        while ($this->cmp($x, '0') > 0) {
            $q = $this->div($x, '58');
            $r = $this->mod($x, '58');
            $output_string .= substr(self::BASE58_CHARS, intval($r), 1);
            $x = $q;
        }

        $data_len = strlen($data);

        for ($i = 0; $i < $data_len && substr($data, $i, 2) == '00'; $i += 2) {
            $output_string .= substr(self::BASE58_CHARS, 0, 1);
        }

        $output_string = strrev($output_string);

        return $output_string;
    }

    /**
     * Decodes $data from BASE-58 format.
     *
     * @param  string $data
     * @return string
     */
    public static function decode($data)
    {
    	$this->argCheck($data);

    	$data_len = strlen($data);
    	$return   = '0';

        for ($i = 0; $i < $data_len; $i++) {
            $current = strpos(self::BASE58_CHARS, $data[$i]);
            $return  = $this->mul($return, '58');
            $return  = $this->add($return, $current);
        }

        $return = Base64::encode($return);

        for ($i = 0; $i < $data_len && substr($data, $i, 1) == '1'; $i++) {
            $return = '00' . $return;
        }

        if (strlen($return) % 2 != 0) {
            $return = '0' . $return;
        }

        return $return;
    }

}
