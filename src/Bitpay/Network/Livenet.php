<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Network;

/**
 * Class used for talking to the "Live" side of
 * the Bitpay payment gateway.  You shouldn't use
 * for testing purposes - use Testnet instead.
 *
 * @package Bitpay
 */
class Livenet extends Network
{
	public function __construct()
	{
		$this->name        = 'livenet';
		$this->addrVersion = '0x00';
		$this->hostURL     = 'bitpay.com';
		$this->hostPort    = 443;
	}

	public function __toString()
	{
		return json_encode(array(
				$this->name,
				$this->addrVersion,
				$this->hostURL,
				$this->hostPort,
		));
	}
}
