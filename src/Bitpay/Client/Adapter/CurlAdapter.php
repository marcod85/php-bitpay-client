<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client\Adapter;

use Bitpay\Client\RequestInterface;
use Bitpay\Client\ResponseInterface;
use Bitpay\Client\Response;

/**
 * Adapter that sends Request objects using CURL. This class makes use of the root cert obtained
 * from the official curl website: http://curl.haxx.se/ca/cacert.pem
 *
 * @package Bitpay
 */
class CurlAdapter implements AdapterInterface
{
    /**
     * @var array
     */
    protected $curlOptions;

    /**
     * @var array
     */
    protected $lastTransferStats;

    /**
     * @param array $curlOptions
     */
    public function __construct(array $curlOptions = array())
    {
        $this->curlOptions = $curlOptions;
        $this->lastTransferStats = array();
    }

    /**
     * Returns an array of curl settings to use
     *
     * @return array
     */
    public function getCurlOptions()
    {
        return $this->curlOptions;
    }

    /**
     * @inheritdoc
     */
    public function sendRequest(RequestInterface $request)
    {
        if (false === isset($request) || true === empty($request)) {
            throw new \Bitpay\Client\ConnectionException('[ERROR] In CurlAdapter::sendRequest(): Missing or invalid $request parameter.');
        }

        $curl = curl_init();

        if (false === isset($curl) || true === empty($curl)) {
            throw new \Bitpay\Client\ConnectionException('[ERROR] In CurlAdapter::sendRequest(): Could not initialize curl.');
        }

        $default_curl_options = $this->getCurlDefaultOptions($request);

        foreach ($this->getCurlOptions() as $curl_option_key => $curl_option_value) {
            if (false === is_null($curl_option_value)) {
                $default_curl_options[$curl_option_key] = $curl_option_value;
            }
        }

        curl_setopt_array($curl, $default_curl_options);

        if (RequestInterface::METHOD_POST == $request->getMethod()) {
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_POST           => 1,
                    CURLOPT_POSTFIELDS     => $request->getBody(),
                )
            );
        }

        $raw = curl_exec($curl);

        if (false === $raw) {
            $errorMessage = curl_error($curl);
            curl_close($curl);
            throw new \Bitpay\Client\ConnectionException('[ERROR] In CurlAdapter::sendRequest(): ' . $errorMessage);
        }

        /** @var ResponseInterface */
        $response = Response::createFromRawResponse($raw);

        if (false === isset($response) || true === empty($response)) {
            throw new \Bitpay\Client\ConnectionException('[ERROR] In CurlAdapter::sendRequest(): Could not create response object from raw curl data.');
        }

        $this->lastTransferStats = curl_getinfo($curl);

        curl_close($curl);

        return $response;
    }

    /**
     * Returns an array of default curl settings to use
     *
     * @param RequestInterface $request
     * @return array
     */
    private function getCurlDefaultOptions(RequestInterface $request)
    {
        if (false === isset($request) || true === empty($request)) {
            throw new \Bitpay\Client\ConnectionException('[ERROR] In CurlAdapter::getCurlDefaultOptions(): Missing or invalid $request parameter.');
        }

        return array(
            CURLOPT_URL            => $request->getUri(),
            CURLOPT_PORT           => $request->getPort(),
            CURLOPT_CUSTOMREQUEST  => $request->getMethod(),
            CURLOPT_HTTPHEADER     => $request->getHeaderFields(),
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO         => __DIR__.'/ca-bundle.crt',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FORBID_REUSE   => 1,
            CURLOPT_FRESH_CONNECT  => 1,
            CURLOPT_HEADER         => true,
        );
    }
}
