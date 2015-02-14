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
 * Engine class for the Binary Calculator Math extension.
 * @see http://php.net/manual/en/book.bc.php
 */
final class BcEngine
{
	const SCALE = 0;

    /**
     * Public constructor method.
     */
    public function __construct()
    {
        $this->setScale();
    }

    /**
     * Adds two arbitrary precision integers, BC style.
     *
     * @param  string $a  First number in decimal or hex format.
     * @param  string $b  Second number in decimal or hex format.
     * @return string     The result of the operation.
     */
    public function add($a, $b)
    {
        return bcadd($this->input($a), $this->input($b));
    }

    /**
     * Compares two arbitrary precision integers, BC style.
     * Returns 0 if the two operands are equal, 1 if $a is
     * larger than $b, or -1 if $b is larger than $a.
     *
     * @param  string $a  First number in decimal or hex format.
     * @param  string $b  Second number in decimal or hex format.
     * @return string     The result of the operation.
     */
    public function cmp($a, $b)
    {
        return bccomp($this->input($a), $this->input($b));
    }

    /**
     * Divides two arbitrary precision integers, BC style.
     * Returns $a / $b if $b > 0, null otherwise.
     *
     * @param  string $a  First number in decimal or hex format.
     * @param  string $b  Second number in decimal or hex format.
     * @return string     The result of the operation.
     */
    public function div($a, $b)
    {
        return bcdiv($this->input($a), $this->input($b));
    }

    /**
     * Finds inverse number $inv for $num by modulus $mod, such as:
     *     $inv * $num = 1 (mod $mod)
     *
     * @param  string $num  First number in decimal or hex format.
     * @param  string $mod  Second number in decimal or hex format.
     * @return string       The result of the operation.
     */
    public function inv($num, $mod)
    {
        $num = $this->input($num);
        $mod = $this->input($mod);

        $x = '1';
        $y = '0';

        $num1 = $mod;

        do {
            $tmp = bcmod($num, $num1);
            $q   = bcdiv($num, $num1);

            $num  = $num1;
            $num1 = $tmp;

            $tmp = bcsub($x, bcmul($y, $q));

            $x = $y;
            $y = $tmp;
        } while (bccomp($num1, '0'));

        if (bccomp($x, '0') < 0) {
            $x = bcadd($x, $mod);
        }

        if (substr($num, 0, 1) === '-') {
            $x = bcsub($mod, $x);
        }

        return $x;
    }

    /**
     * Finds the modulus of $a mod $b, BC style.
     * Returns null if $b == 0.
     *
     * @param  string $a  First number in decimal or hex format.
     * @param  string $b  Second number in decimal or hex format.
     * @return string     The result of the operation.
     */
    public function mod($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        if (substr($a, 0, 1) === '-') {
            return bcadd(bcmod($a, $b), $b);
        }

        return bcmod($a, $b);
    }

    /**
     * Multiplies two arbitrary precision integers, BC style.
     *
     * @param  string $a  First number in decimal or hex format.
     * @param  string $b  Second number in decimal or hex format.
     * @return string     The result of the operation.
     */
    public function mul($a, $b)
    {
        return bcmul($this->input($a), $this->input($b));
    }

    /**
     * Raises an arbitrary precision integer $a to the power of $b, BC style.
     *
     * @param  string $a  First number in decimal or hex format.
     * @param  string $b  Second number in decimal or hex format.
     * @return string     The result of the operation.
     */
    public function pow($a, $b)
    {
        return bcpow($this->input($a), $this->input($b));
    }

    /**
     * Subtracts two arbitrary precision integers, BC style.
     *
     * @param  string $a  First number in decimal or hex format.
     * @param  string $b  Second number in decimal or hex format.
     * @return string     The result of the operation.
     */
    public function sub($a, $b)
    {
        return bcsub($this->input($a), $this->input($b));
    }

    /**
     * Utility function for checking, cleaning and converting
     * any non-decimal string formatted numbers to decimal.
     *
     * @param  string $x  The value to sanitize.
     * @return string     Returns either '0' or the sanitized value.
     * @throws \Exception
     */
    public function input($x)
    {
        $sign = '';
        $hex  = '';
        $curr = '';
        $dec  = '';
        $hlen = 0;

        if (false === isset($x) || true === empty($x)) {
            return '0';
        }

        $x = strtolower(trim($x));

        if (preg_match('/^(-?)0x([0-9a-f]+)$/', $x, $matches)) {
            $sign = $matches[1];
            $hex  = $matches[2];

            $hlen = strlen($hex);

            for ($dec = '0', $i = 0; $i < $hlen; $i++) {
                $current = strpos(self::HEX_CHARS, $hex[$i]);
                $dec     = bcadd(bcmul($dec, '16'), $current);
            }

            return $sign.$dec;

        } elseif (preg_match('/^-?[0-9]+$/', $x)) {
            return $x;
        } else {
            throw new \Exception('[ERROR] In BcEngine::input(): The parameter must be a numeric string in decimal or hexadecimal (with leading 0x) format.  Parameter given: ' . "\n" . var_export($x, true));
        }

    }

    /**
     * We're attempting to set the scale for all Binary
     * Calculator math operations to zero. We do not want
     * nor care about any floats in our math lib.
     */
    private function setScale()
    {
    	if (false === bcscale(self::SCALE)) {
    		throw new \Exception('[ERROR] In BcEngine::setScale(): Could not set the scale for BC math operations.');
    	}
    }
}
