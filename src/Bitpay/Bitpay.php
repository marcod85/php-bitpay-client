<?php
/**
 * PHP Client Library for the new cryptographically secure BitPay API.
 *
 * @copyright  Copyright 2011-2014 BitPay, Inc.
 * @author     Integrations Development Team <integrations@bitpay.com>
 * @license    https://raw.githubusercontent.com/bitpay/php-bitpay-client/master/LICENSE The MIT License (MIT)
 * @see        https://github.com/bitpay/php-bitpay-client
 * @package    Bitpay
 * @since      2.0.0
 * @version    2.2.2
 * @filesource
 */

namespace Bitpay;

use Bitpay\Resource\Client;
use Bitpay\Resource\Application;
use Bitpay\Resource\Bill;
use Bitpay\Resource\Buyer;

/**
 * Primary class for working with the bitpay.com payment gateway.
 * Will return an instance of the Client class.
 *
 * @link https://github.com/bitpay/php-bitpay-client/blob/master/src/Bitpay/Bitpay.php
 */
final class Bitpay
{
	const NAME    = 'BitPay PHP-Client';
	const VERSION = '2.2.2';

	private $client;
    private $application;
    private $bill;
    private $buyer;
    private $currency;
    private $invoice;
    private $item;
    private $org;
    private $payout;
    private $token;
    private $user;
    private $debug;

    public function __construct($config = array(), $debug = false)
    {
    	if (false === isset($this->client) || true === empty($this->client)) {
    		$this->client = new Client($config);
    	}
    }

    final public function getVersion()
    {
    	return self::VERSION;
    }

    final public function getName()
    {
    	return self::NAME;
    }

    final public function getClient()
    {
    	return $this->client;
    }

    final public function newClient()
    {
    	return new Client();
    }

    final public function createClient()
    {
    	return $this->newClient();
    }

    final public function createApplication()
    {
    	return new Application();
    }

    final public function newApplication()
    {
    	return $this->createApplication();
    }

    final public function getApplication()
    {
    	return $this->application;
    }

    public function createBill()
    {
    	return new Bill();
    }

    public function getBill()
    {
    	return $this->bill;
    }

    public function createBuyer()
    {
    	return new Buyer();
    }

    public function getBuyer()
    {
        return $this->buyer;
    }



}
