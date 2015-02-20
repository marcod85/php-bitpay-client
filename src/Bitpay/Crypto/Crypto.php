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
	const A  = '0x00';
	const B  = '0x07';
	const G  = '0x0479be667ef9dcbbac55a06295ce870b07029bfcdb2dce28d959f2815b16f81798483ada7726a3c4655da4fbfc0e1108a8fd17b448a68554199c47d08ffb10d4b8';
	const Gx = '0x79be667ef9dcbbac55a06295ce870b07029bfcdb2dce28d959f2815b16f81798';
	const Gy = '0x483ada7726a3c4655da4fbfc0e1108a8fd17b448a68554199c47d08ffb10d4b8';
	const H  = '0x01';
	const N  = '0xfffffffffffffffffffffffffffffffebaaedce6af48a03bbfd25e8cd0364141';
	const P  = '0xfffffffffffffffffffffffffffffffffffffffffffffffffffffffefffffc2f';

	/**
	 * Type 2 (ephemeral)
	 *
	 * @var string
	 */
	const SIN_TYPE = '0x02';

	/**
	 * Always the prefix!
	 * (well, right now)
	 *
	 * @var string
	 */
	const SIN_VERSION = '0x0F';

	/**
	 * @var array
	 */
	private $fingerprintData = array();

	/**
	 * @var string
	 */
	private $fingerprintHash = '';

	/**
	 * @var SinKey
	 */
	private $sin = null;

	/**
	 * @var PrivateKey
	 */
	private $privateKey = null;

	/**
	 * @var string
	 */
	private $hex = '';

	/**
	 * @var string
	 */
	private $dec = '';

	/**
	 * @var string
	 */
	private $id = '';

	/**
	 * MUST be a HEX value
	 *
	 * @var string
	 */
	private $x = '';

	/**
	 * MUST be a HEX value
	 *
	 * @var string
	 */
	private $y = '';

	/**
	 * @var PublicKey
	 */
	private $publicKey = null;

	/**
	 * @var string
	 */
	private $pemEncoded = '';

	/**
	 * @var array
	 */
	private $pemDecoded = array();

	/**
	 * Public constructor method to initialize important class properties.
	 *
	 * @see \Bitpay\Crypto\Math\Math::__construct()
	 */
	public function __construct()
	{
		parent::__construct();

		$this->hasOpenSSLSupport();
		$this->hasHashSupport();
		$this->hasMcryptSupport();
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
	 * Returns the fingerprint parameter for the Crypto object.
	 *
	 * @return string
	 */
	public function getFingerprint()
	{
		if (true === empty($this->fingerprintHash)) {
            $this->generateFingerprint();
		}

		return $this->fingerprintHash;
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
	 * Decodes PEM data to retrieve the keypair.
	 *
	 * @param  string $pem_data The data to decode.
	 * @return array            The keypair info.
	 * @throws \Exception
	 */
	public function pemDecode($pem_data)
	{
		$this->argCheck($pem_data);

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
	 * Assigns the Crypto::privateKey property
	 * to an instance of a PrivateKey object.
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
	 * Assigns the Crypto::publicKey property
	 * to an instance of a PublicKey object.
	 *
	 * @param  PublicKey
	 * @return self
	 */
	public function setPublicKey(PublicKey $publicKey)
	{
		$this->publicKey = $publicKey;

		return $this;
	}

	/**
	 * Either returns existing or generates a new public key object.
	 *
	 * @return PublicKey
	 */
	public function getPublicKey()
	{
		if (null === $this->publicKey) {
			$this->publicKey = new PublicKey();

			$this->argCheck($this->publicKey);

			$this->publicKey->setPrivateKey($this);
			$this->publicKey->generate();
		}

		return $this->publicKey;
	}

	/**
	 * Either returns existing or generates a new SIN object.
	 *
	 * @return Sin
	 */
	public function getSin()
	{
		if (null === $this->sin) {
			$this->sin = new Sin();

			$this->argCheck($this->sin);

			$this->sin->setPublicKey($this);
			$this->sin->generate();
		}

		return $this->sin;
	}

	/**
	 * Generates a string of environment information and
	 * takes the hash of that value to use as the env
	 * fingerprint.
	 *
	 * @return string
	 */
	private function generateFingerprint()
	{
		if (true === empty($this->fingerprintHash)) {
			return $this->fingerprintHash;
		}

		$finHash = '';
		$sigData = array();

		$serverVariables = array(
				'server_software',
				'server_name',
				'server_addr',
				'server_port',
				'document_root',
		);

		foreach ($_SERVER as $k => $v) {
			if (in_array(strtolower($k), $serverVariables)) {
				$sigData[] = $v;
			}
		}

		$sigData[] = phpversion();
		$sigData[] = get_current_user();
		$sigData[] = php_uname('s') . php_uname('n') . php_uname('m') . PHP_OS . PHP_SAPI . ICONV_IMPL . ICONV_VERSION;
		$sigData[] = sha1_file(__FILE__);

		$finHash = implode($sigData);
		$finHash = sha1(str_ireplace(' ', '', $finHash) . strlen($finHash) . metaphone($finHash));
		$finHash = sha1($finHash);

		return $finHash;
	}

}
