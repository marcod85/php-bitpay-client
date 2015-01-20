<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

use Bitpay\Util\Secp256k1;
use Bitpay\Util\Util;
use Bitpay\Util\SecureRandom;
use Bitpay\Math\Math;

/**
 * @package Bitcore
 * @see https://en.bitcoin.it/wiki/List_of_address_prefixes
 */
class PrivateKey extends Key
{
    /**
     * @var PublicKey
     */
    protected $publicKey;

    /**
     * @var string
     */
    public $pemEncoded = '';

    /**
     * @var array
     */
    public $pemDecoded = array();

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->hex;
    }

    /**
     * @return PublicKey
     */
    public function getPublicKey()
    {
        if (null === $this->publicKey) {
            $this->publicKey = new PublicKey();
            
            if (false === isset($this->publicKey) || true === empty($this->publicKey)) {
                throw new \Exception('[ERROR] In PrivateKey::getPublicKey: Could not instantiate new PublicKey object.');
            }

            $this->publicKey->setPrivateKey($this);
            $this->publicKey->generate();
        }

        return $this->publicKey;
    }

    /**
     * Generates an EC private key
     *
     * @return \Bitpay\PrivateKey
     */
    public function generate()
    {
        if (true === isset($this->hex) && false === empty($this->hex)) {
            return $this;
        }

        do {
            $privateKey = \Bitpay\Util\SecureRandom::generateRandom(32);
            $this->hex  = strtolower(bin2hex($privateKey));
        } while (Math::cmp('0x' . $this->hex, '1') <= 0 || Math::cmp('0x' . $this->hex, '0x' . Secp256k1::N) >= 0);

        $this->dec = Util::decodeHex($this->hex);

        if (Math::cmp($this->dec, '0') <= 0) {
            throw new \Exception('[ERROR] In PrivateKey::generate(): Error decoding hex value. The decimal value returned was <= 0.');
        }

        return $this;
    }

    /**
     * Checks to see if the private key value is not empty and
     * the hex form only contains hexits and the decimal form
     * only contains devimal digits.
     *
     * @return boolean
     */
    public function isValid()
    {
        return ($this->hasValidDec() && $this->hasValidHex());
    }

    /**
     * @return boolean
     */
    public function hasValidHex()
    {
        return (true === isset($this->hex) && false === empty($this->hex) && true === ctype_xdigit($this->hex));
    }

    /**
     * @return boolean
     */
    public function hasValidDec()
    {
        return (true === isset($this->dec) && false === empty($this->dec) && true === ctype_digit($this->dec));
    }

    /**
     * Creates an ECDSA signature of $message
     *
     * @return string
     */
    public function sign($data)
    {
        if (true === isset($this->hex) && false === ctype_xdigit($this->hex)) {
            throw new \Exception('[ERROR] In PrivateKey:sign(): The private key must be in hex format.');
        }

        if (false === isset($data) || true === empty($data)) {
            throw new \Exception('[ERROR] In PrivateKey:sign(): You did not provide any data to sign.');
        }

        $e = Util::decodeHex(hash('sha256', $data));

        if (Math::cmp($e, '0') <= 0) {
            throw new \Exception('[ERROR] In PrivateKey::sign(): Error decoding hex value. The decimal value returned was <= 0.');
        }

        do {
            if (substr(strtolower(trim($this->hex)), 0, 2) != '0x') {
                $d = '0x' . $this->hex;
            } else {
                $d = $this->hex;
            }

            $k = SecureRandom::generateRandom(32);

            if (false === isset($k) || true === empty($k)) {
                throw new \Exception('[ERROR] In PrivateKey:sign(): Failed to generate a secure random value.');
            }

            $k_hex = '0x' . strtolower(bin2hex($k));
            $n_hex = '0x' . Secp256k1::N;

            $Gx = '0x' . substr(Secp256k1::G, 2, 64);
            $Gy = '0x' . substr(Secp256k1::G, 66, 64);

            $P = new Point($Gx, $Gy);

            if (false === isset($P) || true === empty($P)) {
                throw new \Exception('[ERROR] In PrivateKey:sign(): Failed to create a new Point object.');
            }

            // Calculate a new curve point from Q=k*G (x1,y1)
            $R = Util::doubleAndAdd($k_hex, $P);

            if (false === isset($R) || true === empty($R)) {
                throw new \Exception('[ERROR] In PrivateKey:sign(): Failed to calculate a new curve point.');
            }

            $Rx_hex = Util::encodeHex($R->getX());

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
                'r' => Util::encodeHex($r),
                's' => Util::encodeHex($s),
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
     * @param string
     * @param string
     * @return string
     */
    public static function serializeSig($r, $s)
    {
        if (false === isset($r) || true === empty($r) {
            throw new \Exception('[ERROR] In PrivateKey:serializeSig(): Missing or invalid r component.');
        }

        if (false === isset($s) || true === empty($s) {
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

        $dec = Util::decodeHex($r);

        if (Math::cmp($e, '0') <= 0) {
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
        $dec = Util::decodeHex($s);

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

    /**
     * Decodes PEM data to retrieve the keypair.
     *
     * @param  string $pem_data The data to decode.
     * @return array            The keypair info.
     */
    public function pemDecode($pem_data)
    {
        if (false === isset($pem_data) || true === empty($pem_data) {
            throw new \Exception('[ERROR] In PrivateKey:pemDecode(): Missing or invalid pem_data parameter.');
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
            throw new \Exception('[ERROR] In PrivateKey:pemDecode(): Invalid or corrupt secp256k1 key provided. Cannot decode the supplied PEM data.');
        }

        $ecpemstruct = array(
                'oct_sec_val'  => substr($decoded, 14, 64),
                'obj_id_val'   => substr($decoded, 86, 10),
                'bit_str_val'  => substr($decoded, 106),
        );

        if ($ecpemstruct['obj_id_val'] != '2b8104000a') {
            throw new \Exception('[ERROR] In PrivateKey:pemDecode(): Invalid or corrupt secp256k1 key provided. Cannot decode the supplied PEM data.');
        }

        $private_key = $ecpemstruct['oct_sec_val'];
        $public_key  = $ecpemstruct['bit_str_val'];

        if (strlen($private_key) < 64 || strlen($public_key) < 128) {
            throw new \Exception('[ERROR] In PrivateKey:pemDecode(): Invalid or corrupt secp256k1 key provided. Cannot decode the supplied PEM data.');
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
            throw new \Exception('[ERROR] In PrivateKey:pemEncode(): Invalid or corrupt secp256k1 keypair provided. Cannot decode the supplied PEM data.');
        }

        $dec         = '';
        $byte        = '';
        $beg_ec_text = '';
        $end_ec_text = '';
        $ecpemstruct = array();
        $digits      = array();

        for ($x = 0; $x < 256; $x++) {
            $digits[$x] = chr($x);
        }

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
                'bit_str_val'  => '00'.$keypair[1],
        );

        $beg_ec_text = '-----BEGIN EC PRIVATE KEY-----';
        $end_ec_text = '-----END EC PRIVATE KEY-----';

        $dec = trim(implode($ecpemstruct));

        if (strlen($dec) < 230) {
            throw new \Exception('[ERROR] In PrivateKey:pemEncode(): Invalid or corrupt secp256k1 keypair provided. Cannot encode the supplied data.');
        }

        $dec = Util::decodeHex('0x'.$dec);

        if (Math::cmp($dec, '0') <= 0) {
            throw new \Exception('[ERROR] In PrivateKey::pemEncode(): Error decoding hex value. The decimal value returned was <= 0.');
        }

        while (Math::cmp($dec, '0') > 0) {
            $dv   = Math::div($dec, '256');
            $rem  = Math::mod($dec, '256');
            $dec  = $dv;
            $byte = $byte . $digits[$rem];
        }

        $byte = $beg_ec_text . "\r\n" . chunk_split(base64_encode(strrev($byte)), 64) . $end_ec_text;

        $this->pemEncoded = $byte;

        return $byte;
    }
}
