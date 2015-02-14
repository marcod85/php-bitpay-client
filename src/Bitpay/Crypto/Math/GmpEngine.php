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
 * Engine class for the GNU Multiple Precision Math extension.
 * @see http://php.net/manual/en/book.gmp.php
 */
final class GmpEngine
{
    /**
     * Adds two arbitrary precision integers.
     *
     * @param  string $a  First number in decimal or hex format.
     * @param  string $b  Second number in decimal or hex format.
     * @return string     The result of the operation.
     */
    public function add($a, $b)
    {
        return gmp_strval(gmp_add($this->input($a), $this->input($b)));
    }

    /**
     * Compares two arbitrary precision integers. Returns a positive
     * value if $a > $b, zero if $a = $b and a negative value if $a < $b.
     *
     * @param  string $a  First number in decimal or hex format.
     * @param  string $b  Second number in decimal or hex format.
     * @return string     The result of the operation.
     */
    public function cmp($a, $b)
    {
        return gmp_strval(gmp_cmp($this->input($a), $this->input($b)));
    }

    /**
     * Divides two arbitrary precision integers. Returns $a / $b
     * if $b > 0, '0' otherwise. NOTE: Only throws a warning if
     * $b == 0, not an error!
     *
     * @param  string $a  First number in decimal or hex format.
     * @param  string $b  Second number in decimal or hex format.
     * @return string     The result of the operation.
     */
    public function div($a, $b)
    {
        return gmp_strval(gmp_div($this->input($a), $this->input($b)));
    }

    /**
     * Finds inverse number $inv for $num by modulus $mod, such as:
     *     $inv * $num = 1 (mod $mod)
     *
     * @param  string $a  First number in decimal or hex format.
     * @param  string $b  Second number in decimal or hex format.
     * @return string     The result of the operation.
     */
    public function inv($a, $b)
    {
        return gmp_strval(gmp_invert($this->input($a), $this->input($b)));
    }

    /**
     * Finds the modulus of $a mod $b. NOTE: Only throws a warning if
     * $b == 0, not an error!
     *
     * @param  string $a  First number in decimal or hex format.
     * @param  string $b  Second number in decimal or hex format.
     * @return string     The result of the operation.
     */
    public function mod($a, $b)
    {
        return gmp_strval(gmp_mod($this->input($a), $this->input($b)));
    }

    /**
     * Multiplies two arbitrary precision integers.
     *
     * @param  string $a  First number in decimal or hex format.
     * @param  string $b  Second number in decimal or hex format.
     * @return string     The result of the operation.
     */
    public function mul($a, $b)
    {
        return gmp_strval(gmp_mul($this->input($a), $this->input($b)));
    }

    /**
     * Raises an arbitrary precision integer $a to the power of $b.
     *
     * @param  string $a  First number in decimal or hex format.
     * @param  string $b  Second number in decimal or hex format.
     * @return string     The result of the operation.
     */
    public function pow($a, $b)
    {
        return gmp_strval(gmp_pow($this->input($a), $this->input($b)));
    }

    /**
     * Subtracts two arbitrary precision integers.
     *
     * @param  string $a  First number in decimal or hex format.
     * @param  string $b  Second number in decimal or hex format.
     * @return string     The result of the operation.
     */
    public function sub($a, $b)
    {
        return gmp_strval(gmp_sub($this->input($a), $this->input($b)));
    }

    /**
     * Utility function for checking, cleaning and converting
     * any non-hex string formatted numbers to hexadecimal.
     *
     * @param  string $x   The value to sanitize.
     * @return string      Returns either '0x0' or the sanitized value.
     * @throws \Exception
     */
    private function input($x)
    {
        if (false === isset($x) || true === empty($x)) {
            return '0x0';
        }

        $x = strtolower(trim($x));

        if (false === ctype_xdigit($x) && true === ctype_digit($x)) {
            // decimal number
            return $x;
        } else if (substr($x, 0, 2) != '0x' && true === ctype_xdigit($x)) {
            // hex without '0x'
            return '0x' . $x;
        } else if (substr($x, 0, 2) == '0x' && true === ctype_xdigit(substr($x, 2))) {
            // correctly formatted hex
            return $x;
        } else {
            // bad value
            throw new \Exception('[ERROR] In GmpEngine::input(): The parameter must be a numeric string in hexadecimal format.  Parameter given: ' . "\n" . var_export($x, true));
        }

    }
}
