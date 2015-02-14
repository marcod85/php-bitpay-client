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
 * Abstract class for cryptographic extensions.
 */
abstract class Crypto extends Math\Math
{
	use Hash, OpenSSL, Mcrypt;

	/**
	 * Elliptic curve parameters for secp256k1, see:
	 * http://www.secg.org/collateral/sec2_final.pdf
	 * also: https://en.bitcoin.it/wiki/Secp256k1
	 */
	const A = '00';
	const B = '07';
	const G = '0479BE667EF9DCBBAC55A06295CE870B07029BFCDB2DCE28D959F2815B16F81798483ADA7726A3C4655DA4FBFC0E1108A8FD17B448A68554199C47D08FFB10D4B8';
	const H = '01';
	const N = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141';
	const P = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F';

	/**
	 * Type 2 (ephemeral)
	 *
	 * @var string
	 */
	const SIN_TYPE = '02';

	/**
	 * Always the prefix!
	 * (well, right now)
	 *
	 * @var string
	 */
	const SIN_VERSION = '0F';

	/**
	 * @var string
	 */
	private $sigData;

	/**
	 * @var string
	 */
	private $finHash;

	/**
	 * @var boolean
	 */
	private $hasOpenSSL;

	/**
	 * @var SinKey
	 */
	private $sin;

	/**
	 * @var PrivateKey
	 */
	private $privateKey;

	/**
	 * @var string
	 */
	private $hex;

	/**
	 * @var string
	 */
	private $dec;

	/**
	 * @var string
	 */
	private $id;

	/**
	 * MUST be a HEX value
	 *
	 * @var string
	 */
	private $x;

	/**
	 * MUST be a HEX value
	 *
	 * @var string
	 */
	private $y;

	/**
	 * @var PublicKey
	 */
	private $publicKey;

	/**
	 * @var string
	 */
	private $pemEncoded = '';

	/**
	 * @var array
	 */
	private $pemDecoded = array();

	/**
	 * Returns the 'a' curve param in hex.
	 *
	 * @return string
	 */
	public function aHex()
	{
		return '0x' . strtolower(self::A);
	}

	/**
	 * Returns the 'a' curve param in hex.
	 *
	 * @return string
	 */
	public function bHex()
	{
		return '0x' . strtolower(self::B);
	}

	/**
	 * Returns the 'a' curve param in hex.
	 *
	 * @return string
	 */
	public function gHex()
	{
		return '0x' . strtolower(self::G);
	}

	/**
	 * Returns the 'a' curve param in hex.
	 *
	 * @return string
	 */
	public function gxHex()
	{
		return '0x' . substr(strtolower(self::G), 0, 64);
	}

	/**
	 * Returns the 'a' curve param in hex.
	 *
	 * @return string
	 */
	public function gyHex()
	{
		return '0x' . substr(strtolower(self::G), 66, 64);
	}

	/**
	 * Returns the 'a' curve param in hex.
	 *
	 * @return string
	 */
	public function hHex()
	{
		return '0x' . strtolower(self::H);
	}

	/**
	 * Returns the 'a' curve param in hex.
	 *
	 * @return string
	 */
	public function nHex()
	{
		return '0x' . strtolower(self::N);
	}

	/**
	 * Returns the 'a' curve param in hex.
	 *
	 * @return string
	 */
	public function pHex()
	{
		return '0x' . strtolower(self::P);
	}

	/**
	 * Returns the id parameter for this object.
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Returns the hex parameter for this object.
	 *
	 * @return string
	 */
	public function getHex()
	{
		return $this->hex;
	}

	/**
	 * Returns the decimal parameter for this object.
	 *
	 * @return string
	 */
	public function getDec()
	{
		return $this->dec;
	}

	/**
	 * Returns the x-coordinate parameter for this object.
	 *
	 * @return string
	 */
	public function getX()
	{
		return $this->x;
	}

	/**
	 * Returns the y-coordinate parameter for this object.
	 *
	 * @return string
	 */
	public function getY()
	{
		return $this->y;
	}

	/**
	 * Checks to see if this object has been generated.
	 *
	 * @return boolean
	 */
	public function isGenerated()
	{
		return (!empty($this->hex));
	}

	/**
	 * Generates a cryptographically secure random number using
	 * the OpenSSL extension for PHP. Otherwise, throw exception.
	 *
	 * @param  int        $bytes  The number of random bytes to generate.
	 * @return string
	 * @throws \Exception
	 */
	public function generateRandom($bytes = 32)
	{
		if (!$this->hasOpenSSL()) {
			throw new \Exception('[ERROR] In PrivateKey::generateRandom(): The OpenSSL extension is missing or too old.');
		}

		$random = openssl_random_pseudo_bytes($bytes, $isStrong);

		if (!$random || !$isStrong) {
			throw new \Exception('[ERROR] In PrivateKey::generateRandom(): Could not generate a cryptographically strong random number.');
		}

		return $random;
	}

	/**
	 * Decodes PEM data to retrieve the keypair.
	 *
	 * @param  string $pem_data The data to decode.
	 * @return array            The keypair info.
	 * @throws \Exception
	 */
	public function pemDecode($pem_data)
	{
		if (false === isset($pem_data) || true === empty($pem_data)) {
			throw new \Exception('[ERROR] In PrivateKey::pemDecode(): Missing or invalid pem_data parameter.');
		}

		$beg_ec_text = '-----BEGIN EC PRIVATE KEY-----';
		$end_ec_text = '-----END EC PRIVATE KEY-----';

		$decoded = '';

		$ecpemstruct = array();

		$pem_data = str_ireplace($beg_ec_text, '', $pem_data);
		$pem_data = str_ireplace($end_ec_text, '', $pem_data);
		$pem_data = str_ireplace("\r", '', trim($pem_data));
		$pem_data = str_ireplace("\n", '', trim($pem_data));
		$pem_data = str_ireplace(' ', '', trim($pem_data));

		$decoded = bin2hex(base64_decode($pem_data));

		if (strlen($decoded) < 230) {
			throw new \Exception('[ERROR] In PrivateKey::pemDecode(): Invalid or corrupt secp256k1 key provided. Cannot decode the supplied PEM data.');
		}

		$ecpemstruct = array(
				'oct_sec_val' => substr($decoded, 14, 64),
				'obj_id_val'  => substr($decoded, 86, 10),
				'bit_str_val' => substr($decoded, 106),
		);

		if ($ecpemstruct['obj_id_val'] != '2b8104000a') {
			throw new \Exception('[ERROR] In PrivateKey::pemDecode(): Invalid or corrupt secp256k1 key provided. Cannot decode the supplied PEM data.');
		}

		$private_key = $ecpemstruct['oct_sec_val'];
		$public_key  = $ecpemstruct['bit_str_val'];

		if (strlen($private_key) < 64 || strlen($public_key) < 128) {
			throw new \Exception('[ERROR] In PrivateKey::pemDecode(): Invalid or corrupt secp256k1 key provided. Cannot decode the supplied PEM data.');
		}

		$this->pemDecoded = array('private_key' => $private_key, 'public_key' => $public_key);

		return $this->pemDecoded;
	}

	/**
	 * Encodes keypair data to PEM format.
	 *
	 * @param  array  $keypair The keypair info.
	 * @return string          The data to decode.
	 */
	public function pemEncode($keypair)
	{
		if (false === isset($keypair) || (true === is_array($keypair) && (strlen($keypair[0]) < 64) || strlen($keypair[1]) < 128)) {
			throw new \Exception('[ERROR] In PrivateKey::pemEncode(): Invalid or corrupt secp256k1 keypair provided. Cannot decode the supplied PEM data.');
		}

		$dec         = '';
		$byte        = '';
		$beg_ec_text = '';
		$end_ec_text = '';
		$ecpemstruct = array();

		$ecpemstruct = array(
				'sequence_beg' => '30',
				'total_len'    => '74',
				'int_sec_beg'  => '02',
				'int_sec_len'  => '01',
				'int_sec_val'  => '01',
				'oct_sec_beg'  => '04',
				'oct_sec_len'  => '20',
				'oct_sec_val'  => $keypair[0],
				'a0_ele_beg'   => 'a0',
				'a0_ele_len'   => '07',
				'obj_id_beg'   => '06',
				'obj_id_len'   => '05',
				'obj_id_val'   => '2b8104000a',
				'a1_ele_beg'   => 'a1',
				'a1_ele_len'   => '44',
				'bit_str_beg'  => '03',
				'bit_str_len'  => '42',
				'bit_str_val'  => '00' . $keypair[1],
		);

		$beg_ec_text = '-----BEGIN EC PRIVATE KEY-----';
		$end_ec_text = '-----END EC PRIVATE KEY-----';

		$hex = '0x' . trim(implode($ecpemstruct));

		if (strlen($hex) < 230) {
			throw new \Exception('[ERROR] In Crypto::pemEncode(): Invalid or corrupt secp256k1 keypair provided. Cannot encode the supplied data.');
		}

		$byte = $this->baseConvert($hex, '256');

		if ($this->cmp($byte, '0') <= 0) {
			throw new \Exception('[ERROR] In Crypto::pemEncode(): Error converting hex value to byte array. The value returned was <= 0.');
		}

		$byte = $beg_ec_text . "\r\n" . chunk_split(base64_encode(strrev($byte)), 64) . $end_ec_text;

		$this->pemEncoded = $byte;

		return $byte;
	}

	/**
	 * Assigns the object to the privateKey property.
	 *
	 * @param  PrivateKey
	 * @return self
	 */
	public function setPrivateKey(PrivateKey $privateKey)
	{
		$this->privateKey = $privateKey;

		return $this;
	}

	/**
	 * @param  PublicKey
	 * @return self
	 */
	public function setPublicKey(PublicKey $publicKey)
	{
		$this->publicKey = $publicKey;

		return $this;
	}

	/**
	 * @return PublicKey
	 */
	public function getPublicKey()
	{
		if (null === $this->publicKey) {
			$this->publicKey = new PublicKey();

			if (false === isset($this->publicKey) || true === empty($this->publicKey)) {
				throw new \Exception('[ERROR] In Crypto::getPublicKey: Could not instantiate new PublicKey object.');
			}

			$this->publicKey->setPrivateKey($this);
			$this->publicKey->generate();
		}

		return $this->publicKey;
	}

	/**
	 * @return Sin
	 */
	public function getSin()
	{
		if (null === $this->sin) {
			$this->sin = new Sin();

			if (false === isset($this->publicKey) || true === empty($this->publicKey)) {
				throw new \Exception('[ERROR] In Crypto::getSin: Could not instantiate new SIN object.');
			}

			$this->sin->setPublicKey($this);
			$this->sin->generate();
		}

		return $this->sin;
	}

	/**
	 * @return boolean
	 */
	private function hasOpenSSL()
	{
		if (null === self::$hasOpenSSL) {
			self::$hasOpenSSL = extension_loaded('openssl');
		}

		return self::$hasOpenSSL;
	}
}
