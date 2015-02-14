<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Network;

/**
 * Class used for talking to the "Test" side of
 * the Bitpay payment gateway.  Be sure to use
 * testnet bitcoins when paying test invoices!
 *
 * @package Bitpay
 */
class Testnet implements Network
{
	private $name;
	private $addrVersion;
	private $hostURL;
	private $hostPort;

	public function __construct()
	{
		$this->name = 'testnet';
		$this->addrVersion = '0x6F';
		$this->hostURL = 'test.bitpay.com';
		$this->hostPort = '443';
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

	/**
	 * @inheritdoc
	 */
    public function getName()
    {
        return self::TESTNET_NAME;
    }

    /**
     * @inheritdoc
     */
    public function getAddressVersion()
    {
        return self::TESTNET_ADDR;
    }

    /**
     * @inheritdoc
     */
    public function getApiHost()
    {
        return self::TESTNET_URL;
    }

    /**
     * @inheritdoc
     */
    public function getApiPort()
    {
        return self::HTTPS_PORT;
    }
}
