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

namespace Bitpay\Network;

use Bitpay\Network\Adapter;

/**
 * Request object used to send requests to the gateway.
 *
 * @package Bitpay
 */
class Request extends Network
{
    /**
     * Public constructor method to initialize class properties.
     *
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter = null)
    {
        $this->headers = array(
            'Content-Type'         => 'application/json',
            'X-BitPay-Plugin-Info' => 'Bitpay PHP-Client/2.0.0',
        );

        $this->adapter = $adapter;
        $this->port    = 443;
        $this->schema  = 'https';
        $this->method  = self::METHOD_POST;
    }

    /**
     * Converts this request into a standard HTTP/1.1 message to be sent over
     * the wire
     *
     * @return string
     */
    public function __toString()
    {
        $request = sprintf("%s %s HTTP/1.1\r\n", $this->getMethod(), $this->getUriWithPort());
        $request .= $this->getHeadersAsString();
        $request .= $this->getBody();

        return trim($request);
    }
}
