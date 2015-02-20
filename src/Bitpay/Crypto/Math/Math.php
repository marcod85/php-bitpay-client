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
 * Math interface class which handles the routing to the correct engine,
 * either GMP or BC, for all math operations with the former preferred.
 */
final class Math extends Number
{
    private $engine  = null;
    private $encoder = null;

    /**
     * Public constructor method to initialize important class properties.
     *
	 * @see \Bitpay\Crypto\Math\Number::__construct()
	 */
	public function __construct() {
		parent::__construct();

	    if (extension_loaded('gmp')) {
            $this->engine = new GmpEngine();
        } else if (extension_loaded('bcmath')) {
        	$this->engine = new BcEngine();
        } else {
        	$this->engCheck();
        }

        if ($this->encoder == null) {
        	$this->engCheck();
        }
	}

    /**
     * Returns the Math::$engine object or null if not set.
     *
     * @return object|null  Can be null, otherwise returns a GMP or BC Math engine object.
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Adds two arbitrary precision numbers.
     *
     * @param  string $a First number in operation.
     * @param  string $b Second number in operation.
     * @return string    Result of mathematical operation.
     */
    public function add($a, $b)
    {
    	if (strlen($a) < $this->maxint && strlen($b) < $this->maxint) {
            return (int)(intval($a) + intval($b));
    	} else {
            return $this->engine->add($a, $b);
    	}
    }

    /**
     * Compares two arbitrary precision numbers.
     *
     * @param  string $a First number in operation.
     * @param  string $b Second number in operation.
     * @return string    Result of mathematical operation.
     */
    public function cmp($a, $b)
    {
        if (strlen($a) < $this->maxint && strlen($b) < $this->maxint) {
    	    if ($a > $b) {
    		    return 1;
    	    }

    	    if ($b > $a) {
    		    return -1;
    	    }

    	    return 0;

    	} else {
            return $this->engine->cmp($a, $b);
    	}
    }

    /**
     * Divides two arbitrary precision numbers.
     *
     * @param  string $a First number in operation.
     * @param  string $b Second number in operation.
     * @return string    Result of mathematical operation.
     */
    public function div($a, $b)
    {
        if (strlen($a) < $this->maxint && strlen($b) < $this->maxint) {
        	if (intval($b) == 0) {
        		throw new \Exception('[ERROR] In Math::div(): Cannot divide by zero.');
        	}

            return (int)(intval($a) / intval($b));

    	} else {
    		if ($this->engine->cmp($b, '0') == 0) {
    			throw new \Exception('[ERROR] In Math::div(): Cannot divide by zero.');
    		}

            return $this->engine->div($a, $b);
    	}
    }

    /**
     * Calculates the inverse modulo of two arbitrary precision numbers.
     *
     * @param  string $a First number in operation.
     * @param  string $b Second number in operation.
     * @return string    Result of mathematical operation.
     */
    public function inv($a, $b)
    {
    	if (false === $this->coprime($a, $b)) {
    		return null;
    	}

    	$this->engine->inv($a, $b);
    }

    /**
     * Calculates the modulo of two arbitrary precision numbers.
     *
     * @param  string $a First number in operation.
     * @param  string $b Second number in operation.
     * @return string    Result of mathematical operation.
     */
    public function mod($a, $b)
    {
        if (strlen($a) < $this->maxint && strlen($b) < $this->maxint) {
            return (int)(intval($a) % intval($b));
    	} else {
            return $this->engine->mod($a, $b);
    	}
    }

    /**
     * Multiplies two arbitrary precision numbers.
     *
     * @param  string $a First number in operation.
     * @param  string $b Second number in operation.
     * @return string    Result of mathematical operation.
     */
    public function mul($a, $b)
    {
        if (strlen($a) < 5 && strlen($b) < 5) {
            return (int)(intval($a) * intval($b));
    	} else {
            return $this->engine->mul($a, $b);
    	}
    }

    /**
     * Raises the arbitrary precision number $a to $b.
     *
     * @param  string $a First number in operation.
     * @param  string $b Second number in operation.
     * @return string    Result of mathematical operation.
     */
    public function pow($a, $b)
    {
    	$this->engine->pow($a, $b);
    }

    /**
     * Subtracts two arbitrary precision numbers.
     *
     * @param  string $a First number in operation.
     * @param  string $b Second number in operation.
     * @return string    Result of mathematical operation.
     */
    public function sub($a, $b)
    {
        if (strlen($a) < $this->maxint && strlen($b) < $this->maxint) {
            return (int)(intval($a) - intval($b));
    	} else {
            return $this->engine->sub($a, $b);
    	}
    }

    /**
     * Converts one base to another. Current valid values
     * are 2, 8, 10, 16, 58 and 256.
     *
     * @param  string  $number The number to convert.
     * @param  string  $base   The base to convert to.
     * @return string  $value  The converted number.
     * @throws \Exception
     */
    public function baseConvert($number, $base)
    {
    	$this->argCheck($number);

    	// TODO:
    	// if the number is good, determine what base it
    	// is now because to convert from certain bases
    	// we will have to do some specialized processing.

    	$dv     = '';
    	$byte   = '';
    	$rem    = '';
    	$digits = null;

    	$digits = $this->baseCheck($base);

    	while ($this->cmp($number, '0') > 0) {
    		$dv     = $this->div($number, $base);
    		$rem    = $this->mod($number, $base);
    		$number = $dv;
    		$byte   = $byte . $digits[$rem];
    	}

    	// A bit of special processing for base-58 encoding only.
    	if ($base == 58) {
        	for ($i = 0; $i < strlen($byte) && substr($byte, $i, 2) == '00'; $i += 2) {
    		    $byte .= substr($digits, 0, 1);
    	    }

    	    $byte = strrev($byte);
    	}

    	return $byte;
    }

    /**
     * Function to determine if two numbers are
     * co-prime according to the Euclidean algo.
     *
     * @param  string $a  First param to check.
     * @param  string $b  Second param to check.
     * @return bool       The result of the operation.
     * @throws \Exception
     */
    private function coprime($a, $b)
    {
    	$small = 0;
    	$diff  = 0;

    	while ($this->cmp($a, '0') > 0 && $this->cmp($b, '0') > 0) {
    		$comp_result = $this->cmp($a, $b);

    		switch ($comp_result) {
    			case -1:
    				$small = $a;
    				$diff  = $this->mod($b, $a);
    				break;
    			case 1:
    				$small = $b;
    				$diff  = $this->mod($a, $b);
    				break;
    			case 0:
    				$small = $a;
    				$diff  = $this->mod($b, $a);
    				break;
    			default:
    				throw new \Exception('[ERROR] In Math::coprime(): Comparison of the two parameters produced an invalid result.');
    		}

    		$a = $small;
    		$b = $diff;
    	}

    	if ($this->cmp($a, '1') == 0) {
    		return true;
    	}

    	return false;
    }

    /**
     * Checks to see if we have an engine
     * defined and valid or not.
     *
     * @throws \Exception
     */
    private function engCheck()
    {
    	if (false === isset($this->engine) || true === empty($this->engine) || false === is_object($this->engine)) {
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

    		throw new \Exception('[ERROR] ' . $class . $function . $line . ': Missing or invalid math engine - cannot continue!  Please ensure you have either the GMP or BC Math extension installed for PHP.');
    	}
    }
}
