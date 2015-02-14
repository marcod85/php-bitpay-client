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

/**
 * Response object used to parse a response from the gateway.
 *
 * @package Bitpay
 */
class Response extends Network
{
    /**
     * Public constructor method to initialize class properties.
     *
     * @param string $raw
     */
    public function __construct($raw = null)
    {
        $this->headers = array();
        $this->raw     = $raw;
    }

    /**
     * Returns the raw http response
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->raw;
    }

    /**
     */
    public static function createFromRawResponse($rawResponse)
    {
        $response = new self($rawResponse);
        $lines    = preg_split('/(\\r?\\n)/', $rawResponse);
        for ($i = 0; $i < count($lines); $i++) {
            if (0 == $i) {
                preg_match('/^HTTP\/(\d\.\d)\s(\d+)\s(.+)/', $lines[$i], $statusLine);
                $response->setStatusCode($statusCode = $statusLine[2]);
                continue;
            }

            if (empty($lines[$i])) {
                $body = array_slice($lines, $i + 1);
                $response->setBody(implode("\n", $body));
                break;
            }

            if (strpos($lines[$i], ':')) {
                $headerParts = explode(':', $lines[$i]);
                $response->setHeader($headerParts[0], $headerParts[1]);
            }
        }

        return $response;
    }
}
