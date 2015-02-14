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
use Bitpay\Crypto\Math\Base64;

/**
 * Class used for creating and validating cryptographic signatures.
 */
final class Signature extends Crypto
{
	public function __construct()
	{
		//
	}

	/**
	 * Creates an ECDSA signature of $message
	 *
	 * @param  string
	 * @return string
	 * @throws \Exception
	 */
	public function sign($data)
	{
		if (true === isset($this->hex) && false === ctype_xdigit($this->hex)) {
			throw new \Exception('[ERROR] In PrivateKey:sign(): The private key must be in hex format.');
		}

		if (false === isset($data) || true === empty($data)) {
			throw new \Exception('[ERROR] In PrivateKey:sign(): You did not provide any data to sign.');
		}

		$e = Base64::decode(hash('sha256', $data));

		if (Math::cmp($e, '0') <= 0) {
			throw new \Exception('[ERROR] In PrivateKey::sign(): Error decoding hex value. The decimal value returned was <= 0.');
		}

		do {
			if (substr(strtolower(trim($this->hex)), 0, 2) != '0x') {
				$d = '0x' . $this->hex;
			} else {
				$d = $this->hex;
			}

			$k = self::generateRandom(32);

			if (false === isset($k) || true === empty($k)) {
				throw new \Exception('[ERROR] In PrivateKey:sign(): Failed to generate a secure random value.');
			}

			$k_hex = '0x' . strtolower(bin2hex($k));
			$n_hex = '0x' . self::N;

			$Gx = '0x' . substr(self::G, 2, 64);
			$Gy = '0x' . substr(self::G, 66, 64);

			$P = new Point($Gx, $Gy);

			if (false === isset($P) || true === empty($P)) {
				throw new \Exception('[ERROR] In PrivateKey:sign(): Failed to create a new Point object.');
			}

			// Calculate a new curve point from Q=k*G (x1,y1)
			$R = Base64::doubleAndAdd($k_hex, $P);

			if (false === isset($R) || true === empty($R)) {
				throw new \Exception('[ERROR] In PrivateKey:sign(): Failed to calculate a new curve point.');
			}

			$Rx_hex = Base64::encode($R->getX());

			if (Math::cmp($Rx_hex, '0') <= 0) {
				throw new \Exception('[ERROR] In PrivateKey::sign(): Error encoding decimal value. The hex value returned was <= 0.');
			}

			$Rx_hex = str_pad($Rx_hex, 64, '0', STR_PAD_LEFT);

			// r = x1 mod n
			$r = Math::mod('0x' . $Rx_hex, $n_hex);

			// s = k^-1 * (e+d*r) mod n
			$edr  = Math::add($e, Math::mul($d, $r));
			$invk = Math::invertm($k_hex, $n_hex);
			$kedr = Math::mul($invk, $edr);

			$s = Math::mod($kedr, $n_hex);

			// The signature is the pair (r,s)
			$signature = array(
					'r' => Base64::encode($r),
					's' => Base64::encode($s),
			);

			$signature['r'] = str_pad($signature['r'], 64, '0', STR_PAD_LEFT);
			$signature['s'] = str_pad($signature['s'], 64, '0', STR_PAD_LEFT);
		} while (Math::cmp($r, '0') <= 0 || Math::cmp($s, '0') <= 0);

		$sig = array(
				'sig_rs'  => $signature,
				'sig_hex' => self::serializeSig($signature['r'], $signature['s']),
		);

		return $sig['sig_hex']['seq'];
	}

	/**
	 * ASN.1 DER encodes the signature based on the form:
	 * 0x30 + size(all) + 0x02 + size(r) + r + 0x02 + size(s) + s
	 * http://www.itu.int/ITU-T/studygroups/com17/languages/X.690-0207.pdf
	 *
	 * @param  string
	 * @param  string
	 * @return string
	 * @throws \Exception
	 */
	public static function serialize($r, $s)
	{
		if (false === isset($r) || true === empty($r)) {
			throw new \Exception('[ERROR] In PrivateKey:serializeSig(): Missing or invalid r component.');
		}

		if (false === isset($s) || true === empty($s)) {
			throw new \Exception('[ERROR] In PrivateKey:serializeSig(): Missing or invalid s component.');
		}

		$dec  = '';
		$byte = '';
		$seq  = '';

		$digits = array();
		$retval = array();

		for ($x = 0; $x < 256; $x++) {
			$digits[$x] = chr($x);
		}

		$dec = Base64::decode($r);

		if (Math::cmp($dec, '0') <= 0) {
			throw new \Exception('[ERROR] In PrivateKey::serializeSig(): Error decoding hex value. The decimal value returned was <= 0.');
		}

		while (Math::cmp($dec, '0') > 0) {
			$dv   = Math::div($dec, '256');
			$rem  = Math::mod($dec, '256');
			$dec  = $dv;
			$byte = $byte . $digits[$rem];
		}

		$byte = strrev($byte);

		// msb check
		if (Math::cmp('0x' . bin2hex($byte[0]), '0x80') >= 0) {
			$byte = chr(0x00) . $byte;
		}

		$retval['bin_r'] = bin2hex($byte);

		$seq = chr(0x02) . chr(strlen($byte)) . $byte;
		$dec = Base64::decode($s);

		$byte = '';

		while (Math::cmp($dec, '0') > 0) {
			$dv   = Math::div($dec, '256');
			$rem  = Math::mod($dec, '256');
			$dec  = $dv;
			$byte = $byte . $digits[$rem];
		}

		$byte = strrev($byte);

		// msb check
		if (Math::cmp('0x' . bin2hex($byte[0]), '0x80') >= 0) {
			$byte = chr(0x00) . $byte;
		}

		$retval['bin_s'] = bin2hex($byte);

		$seq = $seq . chr(0x02) . chr(strlen($byte)) . $byte;
		$seq = chr(0x30) . chr(strlen($seq)) . $seq;

		$retval['seq'] = bin2hex($seq);

		if (false === isset($retval['seq']) || true === empty($retval['seq'])) {
			throw new \Exception('[ERROR] In PrivateKey:serializeSig(): Failed to serialize the signature coordinates.');
		}

		return $retval;
	}
}
