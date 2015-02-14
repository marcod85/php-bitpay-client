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

/**
 * Utility class for generating an operating system & enivironment fingerprint.
 */
class Fingerprint extends Crypto
{
    /**
     * Generates a string of environment information and
     * takes the hash of that value to use as the env
     * fingerprint.
     *
     * @return string
     */
    final public static function generate()
    {
        if (null !== self::$finHash) {
            return self::$finHash;
        }

        self::$finHash = '';
        self::$sigData = array();

        $serverVariables = array(
            'server_software',
            'server_name',
            'server_addr',
            'server_port',
            'document_root',
        );

        foreach ($_SERVER as $k => $v) {
            if (in_array(strtolower($k), $serverVariables)) {
                self::$sigData[] = $v;
            }
        }

        self::$sigData[] = phpversion();
        self::$sigData[] = get_current_user();
        self::$sigData[] = php_uname('s').php_uname('n').php_uname('m').PHP_OS.PHP_SAPI.ICONV_IMPL.ICONV_VERSION;
        self::$sigData[] = sha1_file(__FILE__);

        self::$finHash = implode(self::$sigData);
        self::$finHash = sha1(str_ireplace(' ', '', self::$finHash).strlen(self::$finHash).metaphone(self::$finHash));
        self::$finHash = sha1(self::$finHash);

        return self::$finHash;
    }
}
