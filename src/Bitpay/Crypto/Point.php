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

/**
 * Object to represent a curve point for our cryptographic operations.
 */
final class Point extends Crypto
{
    /**
     * Public constructor method to initialize class properties.
     *
     * @param string $x
     * @param string $y
     */
    public function __construct($x, $y)
    {
    	parent::__construct();

        if (true === isset($x) && false === empty($x)) {
            $this->x = (string) $x;
        }

        if (true === isset($y) && false === empty($y)) {
            $this->y = (string) $y;
        }
    }

    /**
     * Return the string representation of this Point object.
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->isInfinity()) {
            return self::INFINITY;
        }

        return sprintf('(%s, %s)', $this->x, $this->y);
    }

    /**
     * Returns a storable representation of this Point object.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array($this->x, $this->y));
    }

    /**
     * Takes a single serialized variable and converts it back into a PHP value.
     *
     * @param string
     */
    public function unserialize($data)
    {
        list(
            $this->x,
            $this->y
        ) = unserialize($data);
    }

    /**
     * Implemenation of the Double-and-Add algorithm.
     *
     * @param string $hex
     * @param Point $point
     * @return \Bitpay\Crypto\Point
     */
    public function doubleAndAdd($hex, Point $point)
    {
    	$tmp = self::decToBin($hex);

    	$n   = strlen($tmp) - 1;
    	$S   = new Point(self::INFINITY, self::INFINITY);

    	while ($n >= 0) {
    		$S = self::pointDouble($S);

    		if ($tmp[$n] == 1) {
    			$S = self::pointAdd($S, $point);
    		}

    		$n--;
    	}

    	return new Point($S->getX(), $S->getY());
    }

    /**
     * Point multiplication method 2P = R where
     *   s = (3xP2 + a)/(2yP) mod p
     *   xR = s2 - 2xP mod p
     *   yR = -yP + s(xP - xR) mod p
     *
     * @param  Point
     * @return Point
     * @throws \Exception
     */
    public static function pointDouble(Point $point)
    {
    	if ($point->isInfinity()) {
    		return $point;
    	}

    	$p = $this->pHex();
    	$a = $this->aHex();

    	$s = 0;
    	$R = array(
    			'x' => 0,
    			'y' => 0,
    	);

    	// Critical math section
    	try {
    		$m      = Math::add(Math::mul(3, Math::mul($point->getX(), $point->getX())), $a);
    		$o      = Math::mul(2, $point->getY());
    		$n      = Math::invertm($o, $p);
    		$n2     = Math::mod($o, $p);
    		$st     = Math::mul($m, $n);
    		$st2    = Math::mul($m, $n2);
    		$s      = Math::mod($st, $p);
    		$s2     = Math::mod($st2, $p);
    		$xmul   = Math::mul(2, $point->getX());
    		$smul   = Math::mul($s, $s);
    		$xsub   = Math::sub($smul, $xmul);
    		$xmod   = Math::mod($xsub, $p);
    		$R['x'] = $xmod;
    		$ysub   = Math::sub($point->getX(), $R['x']);
    		$ymul   = Math::mul($s, $ysub);
    		$ysub2  = Math::sub(0, $point->getY());
    		$yadd   = Math::add($ysub2, $ymul);

    		$R['y'] = Math::mod($yadd, $p);

    	} catch (\Exception $e) {
    		throw new \Exception('Error in Util::pointDouble(): '.$e->getMessage());
    	}

    	return new Point($R['x'], $R['y']);
    }

    /**
     * Point addition method P + Q = R where:
     *   s = (yP - yQ)/(xP - xQ) mod p
     *   xR = s2 - xP - xQ mod p
     *   yR = -yP + s(xP - xR) mod p
     *
     * @param  Point
     * @param  Point
     * @return Point
     * @throws \Exception
     */
    public static function pointAdd(Point $P, Point $Q)
    {
    	if ($P->isInfinity()) {
    		return $Q;
    	}

    	if ($Q->isInfinity()) {
    		return $P;
    	}

    	if ($P->getX() == $Q->getX() && $P->getY() == $Q->getY()) {
    		return self::pointDouble(new Point($P->getX(), $P->getY()));
    	}

    	$p = '0x'.self::P;
    	$a = '0x'.self::A;
    	$s = 0;
    	$R = array(
    			'x' => 0,
    			'y' => 0,
    			's' => 0,
    	);

    	// Critical math section
    	try {
    		$m      = Math::sub($P->getY(), $Q->getY());
    		$n      = Math::sub($P->getX(), $Q->getX());
    		$o      = Math::invertm($n, $p);
    		$st     = Math::mul($m, $o);
    		$s      = Math::mod($st, $p);

    		$R['x'] = Math::mod(
    				Math::sub(
    						Math::sub(
    								Math::mul($s, $s),
    								$P->getX()
    						),
    						$Q->getX()
    				),
    				$p
    		);
    		$R['y'] = Math::mod(
    				Math::add(
    						Math::sub(
    								0,
    								$P->getY()
    						),
    						Math::mul(
    								$s,
    								Math::sub(
    										$P->getX(),
    										$R['x']
    								)
    						)
    				),
    				$p
    		);

    		$R['s'] = $s;
    	} catch (\Exception $e) {
    		throw new \Exception('Error in Util::pointAdd(): '.$e->getMessage());
    	}

    	return new Point($R['x'], $R['y']);
    }

}
