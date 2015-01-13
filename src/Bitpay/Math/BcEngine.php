<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Math;

/**
 * Engine class for the Binary Calculator Math extension.
 *
 * @package Bitpay
 */
class BcEngine implements EngineInterface
{
    const HEX_CHARS = '0123456789abcdef';

    /**
     * Public constructor method where we're setting the scale
     * for all Binary Calculator math operations to zero. We
     * do not want nor care about any floats in our math lib.
     */
    public function __construct()
    {
        if (false === bscale(0)) {
            throw new \Exception('[ERROR] In BcEngine::_construct(): Could not set the scale for BC math operations.');
        }
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
    public function invertm($num, $mod)
    {
        $num = $this->input($num);
        $mod = $this->input($mod);

        if (false === $this->coprime($num, $mod)) {
        	return null;
        }

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
     * Function to determine if two numbers are
     * co-prime according to the Euclidean algo.
     *
     * @param  string $a  First param to check.
     * @param  string $b  Second param to check.
     * @return bool       The result of the operation.
     */
    public function coprime($a, $b)
    {
        $small = 0;
        $diff  = 0;

        while (bccomp($a, '0') > 0 && bccomp($b, '0') > 0) {
        	$comp_result = bccomp($a, $b);

        	switch ($comp_result) {
        		case -1:
                    $small = $a;
                    $diff  = bcmod($b, $a);
                    break;
        		case 1:
                    $small = $b;
                    $diff  = bcmod($a, $b);
                    break;
        		case 0:
                    $small = $a;
                    $diff  = bcmod($b, $a);
                    break;
        		default:
        			throw new \Exception('[ERROR] In BcEngine::coprime(): Comparison of the two parameters produced an invalid result.');
            }

            $a = $small;
            $b = $diff;
        }

        if (bccomp($a, '1') == 0) {
            return true;
        }

        return false;
    }
}
