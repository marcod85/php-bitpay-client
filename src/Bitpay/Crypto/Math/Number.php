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
 * Abstract class representing validation and number theory methods.
 */
abstract class Number extends \stdClass
{
	const NUM_DEC = 1;
    const NUM_HEX = 2;
    const NUM_BIN = 3;
    const NUM_B58 = 4;
    const NUM_OCT = 5;
    const NUM_NUL = 0;
    const NUM_BAD = -1;

    const BASE58_CHARS = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
    const HEX_CHARS    = '0123456789abcdef';
    const DEC_CHARS    = '0123456789';
    const OCTAL_CHARS  = '01234567';
    const BINARY_CHARS = '01';

    const INFINITY     = 'infinity';

    /**
     * Value (quantity) of the arbitrary precision number itself.
     *
     * @var string
     */
    private $value = '0';

    /**
     * Type (base) of the number represented.
     *
     * @var string
     */
    private $base = '';

    /**
     * Maximum intger size for this implementation of PHP.
     *
     * @var int
     */
    private $maxint = 5;

    /**
     * Byte array for conversion operations.
     *
     * @var array
     */
    private $digits = array();

	/**
	 * Public constructor method to initialize important class properties.
	 *
	 * @param string $value
	 */
	public function __construct($value = '0')
	{
		if (PHP_INT_SIZE > 4) {
			$this->maxint = 10;
		} else {
			$this->maxint = 5;
		}

		for ($x = 0; $x < 256; $x++) {
			$this->digits[$x] = chr($x);
		}

		if (false === empty($value)) {
			$this->value = $value;
		}
	}

	/**
	 * Checks to see if the $data we're working with is valid or not.
	 *
	 * @param mixed $data
	 */
	public function argCheck($data)
	{
		if (false === isset($data) || true === empty($data) || false === is_string($data) || strlen($data) == 0) {
			$trace    = debug_backtrace();
			$caller   = $trace[1];
            $class    = 'Unknown';
            $function = 'unknown';
            $line     = 'unknown';

			if (true === isset($caller['class'])) {
				$class = 'In ' . $caller['class'] . '::';
			}

			if (true === isset($caller['function'])) {
				$function = $caller['function'] . '()';
			}

			if (true === isset($caller['line'])) {
				$line = ' on line ' . $caller['line'];
			}

			throw new \Exception('[ERROR] ' . $class . $function . $line . ': Missing or invalid parameter passed to function.');
		}
	}

	/**
	 * Returns the appropriate base digit string/array for the
	 * requested base parameter.
	 *
	 * @param  string $base  The base requested.
	 * @return array|string  The base character info.
	 */
	public function baseCheck($base)
	{
		switch ($base) {
			case '256':
				return $this->digits;
			case '16':
				return self::HEX_CHARS;
			case '58':
				return self::BASE58_CHARS;
			case '10':
				return self::DEC_CHARS;
			case '8':
				return self::OCTAL_CHARS;
			case '2':
				return self::BINARY_CHARS;
			default:
				throw new \Exception('[ERROR] In Number::baseCheck(): Unknown base parameter passed to function.');
		}
	}

	/**
	 * Checks to see if the provided value is equal to infinity.
	 *
	 * @return boolean
	 */
	public function isInfinity($value)
	{
		return (self::INFINITY == $value);
	}

	/**
	 * Checks to see if the tested value is not empty and
	 * the hex form only contains hex digits and the decimal
	 * form only contains decimal digits.
	 *
	 * @return boolean
	 */
	public function isValid()
	{
		return ($this->hasValidDec() && $this->hasValidHex());
	}

	/**
	 * Checks to see if this object has a valid hex parameter.
	 *
	 * @return boolean
	 */
	public function hasValidHex()
	{
		return (true === isset($this->hex) && false === empty($this->hex) && true === ctype_xdigit($this->hex));
	}

	/**
	 * Checks to see if this object has a valid decimal parameter.
	 *
	 * @return boolean
	 */
	public function hasValidDec()
	{
		return (true === isset($this->dec) && false === empty($this->dec) && true === ctype_digit($this->dec));
	}

	/**
	 * Attempts to determine what type of number this is.
	 * I.e., is this a hex, decimal, binary, or encoded
	 * value of a different kind.
	 *
	 * @param  string $number
	 * @return string $type
	 * @todo   Fill this in!
	 */
	private function numType($number)
	{
		// TODO
	}
}
