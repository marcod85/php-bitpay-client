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
 * Wrapper around the HASH Message Digest Framework for PHP and includes other related hash functions.
 * @see http://php.net/manual/en/book.hash.php
 */
trait Hash
{
    /**
     * Checks to see if this server has the PHP hash
     * extension installed.
     *
     * @return bool
     */
    final public static function hasSupport()
    {
        return function_exists('hash');
    }

    /**
     * Return a numerically indexed array containing
     * the list of supported hashing algorithms.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @return array
     */
    final public function getAlgos()
    {
        return hash_algos();
    }

    /**
     * Copy a hashing context.
     * (PHP 5 >= 5.3.0)
     *
     * @param  resource
     * @return resource|bool
     */
    final public function copy($context)
    {
        if (false === isset($context) || false === is_resource($context)) {
            return false;
        }

        return hash_copy($context);
    }

    /**
     * Compares two strings using the same time
     * whether they're equal or not. This function
     * should be used to mitigate timing attacks;
     * for instance, when testing crypt() password
     * hashes.
     * (PHP 5 >= 5.6.0)
     *
     * @param  string
     * @param  string
     * @return bool
     */
    final public function equals($string1, $string2)
    {
        if (false === is_string($string1) || false === is_string($string2)) {
            return false;
        }

        return hash_equals($string1, $string2);
    }

    /**
     * Generate a hash value using the contents of
     * a given file. Returns a string containing
     * the calculated message digest as lowercase
     * hexits unless raw_output is set to true in
     * which case the raw binary representation of
     * the message digest is returned.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param  string
     * @param  string
     * @param  bool
     * @return string
     */
    final public function file($algorithm, $filename, $raw_output = false)
    {
        return hash_file($algorithm, (string) $filename, $raw_output);
    }

    /**
     * Finalize an incremental hash and return
     * resulting digest.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param  resource
     * @param  bool
     * @return string|bool
     */
    final public function finalize($context, $raw_output = false)
    {
        if (false === is_resource($context)) {
            return false;
        }

        return hash_final($context, $raw_output);
    }

    /**
     * Generate a keyed hash value using the HMAC
     * method and the contents of a given file.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param  string
     * @param  string
     * @param  string
     * @param  bool
     * @return string
     */
    final public function hmacFile($algorithm, $filename, $key, $raw_output = false)
    {
        return hash_hmac_file($algorithm, $filename, $key, $raw_output);
    }

    /**
     * Generate a keyed hash value using the HMAC
     * method and the message passed via $data.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param  string
     * @param  string
     * @param  string
     * @param  bool
     * @return string
     */
    final public function hmac($algo, $data, $key, $raw_output = false)
    {
        return hash_hmac($algo, $data, $key, $raw_output);
    }

    /**
     * Initialize an incremental hashing context and
     * returns a Hashing Context resource for use with
     * hash_update(), hash_update_stream(), hash_update_file(),
     * and hash_final(). Note: the only option possible
     * for $options at this time is HASH_HMAC. When
     * this is specified, the key *must* be used as well.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param  string
     * @param  int
     * @param  string
     * @return resource
     */
    final public function init($algorithm, $options = 0, $key = null)
    {
        return hash_init($algorithm, $options, $key);
    }

    /**
     * Generate a PBKDF2 key derivation of a supplied
     * password. An E_WARNING will be raised if the
     * algorithm is unknown, the iterations parameter
     * is less than or equal to 0, the length is less
     * than 0 or the salt is too long (greater than
     * INT_MAX - 4). The salt should be generated
     * randomly with openssl_ramdom_pseudo_bytes().
     * (PHP 5 >= 5.5.0)
     *
     * @param  string
     * @param  string
     * @param  string
     * @param  int
     * @param  int
     * @param  bool
     * @return string
     */
    final public function pbkdf2($algo, $password, $salt, $iterations, $length = 0, $raw_output = false)
    {
        return hash_pbkdf2($algo, $password, $salt, $iterations, $length, $raw_output);
    }

    /**
     * Pump data into an active hashing context
     * from a file. Returns TRUE on success or
     * FALSE on failure.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param  resource
     * @param  string
     * @param  resource
     * @return bool
     */
    final public function updateFile($hcontext, $filename, $scontext = null)
    {
        if (false === is_resource($hcontext)) {
            return false;
        }

        return hash_update_file($hcontext, $filename, $scontext);
    }

    /**
     * Pump data into an active hashing context
     * from an open stream. Returns the actual
     * number of bytes added to the hashing
     * context from handle.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param  resource
     * @param  resource
     * @param  int
     * @return int|bool
     */
    final public function updateStream($context, $handle, $length = -1)
    {
        if (false === is_resource($context) || false === is_resource($handle)) {
            return false;
        }

        return hash_update_stream($context, $handle, $length);
    }

    /**
     * Pump data into an active hashing context.
     * The PHP function itself only returns true.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param  resource
     * @param  string
     * @return string|bool
     */
    final public function update($context, $data)
    {
        if (false === is_resource($context)) {
            return false;
        }

        return hash_update($context, $data);
    }

    /**
     * Generate a hash value (message digest)
     * based on the request algorithm and the
     * provided data. Outputs hex unless the
     * $raw_output param is set to true.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param  string
     * @param  string
     * @param  bool
     * @return string|bool
     */
    final public function generate($algo, $data, $raw_output = false)
    {
        if (false === isset($data) || true === empty($data)) {
            return false;
        }

        return hash($algo, $data, $raw_output);
    }

    /**
     * Computes a digest hash value for the given data using
     * the given method, and returns a raw or binhex encoded
     * string, see:
     * http://us1.php.net/manual/en/function.openssl-digest.php
     *
     * @param  string $data
     * @return string
     */
    public static function sha256($data, $binary = false)
    {
    	return openssl_digest($data, 'SHA256', $binary);
    }

    /**
     * Computes a digest hash value for the given data using
     * the given method, and returns a raw or binhex encoded
     * string, see:
     * http://us1.php.net/manual/en/function.openssl-digest.php
     *
     * @param  string $data
     * @return string
     */
    public static function sha512($data)
    {
    	return openssl_digest($data, 'sha512');
    }

    /**
     * Generate a keyed hash value using the HMAC method.
     * http://us1.php.net/manual/en/function.hash-hmac.php
     *
     * @param  string $data
     * @param  string $key
     * @return string
     */
    public static function sha512hmac($data, $key)
    {
    	return hash_hmac('SHA512', $data, $key);
    }

    /**
     * Returns a RIPDEMD160 hash of a value.
     *
     * @param  string $data
     * @return string
     */
    public static function ripe160($data, $binary = false)
    {
    	return openssl_digest($data, 'ripemd160', $binary);
    }

    /**
     * Returns a SHA256 hash of a RIPEMD160 hash of a value.
     *
     * @param  string $data
     * @return string
     */
    public static function sha256ripe160($data)
    {
    	return bin2hex(self::ripe160(self::sha256($data, true), true));
    }

    /**
     * Returns a double SHA256 hash of a value.
     *
     * @param  string $data
     * @return string
     */
    public static function twoSha256($data, $binary = false)
    {
    	return self::sha256(self::sha256($data, $binary), $binary);
    }
}
