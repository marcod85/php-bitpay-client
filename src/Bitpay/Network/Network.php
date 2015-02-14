<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Network;

/**
 * Interface for use by any network-related classes.
 *
 * @package Bitpay
 */
abstract class Network
{
	const METHOD_POST   = 'POST';
	const METHOD_GET    = 'GET';
	const METHOD_PUT    = 'PUT';
	const METHOD_DELETE = 'DELETE';

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $addrVersion;

	/**
	 * @var string
	 */
	private $hostURL;

	/**
	 * @var string
	 */
	private $hostPort;

	/**
	 * @var string
	 */
	private $raw;

	/**
	 * @var array
	 */
	private $headers;

	/**
	 * @var string
	 */
	private $body;

	/**
	 * @var integer
	 */
	private $statusCode;

	/**
	 * @var string
	 */
	private $schema;

	/**
	 * $schema://$hostname/$path
	 *
	 * @var string
	 */
	private $uri;

	/**
	 * @var string
	 */
	private $method;

	/**
	 * This should be something such as `test.bitpay.com` or just `bitpay.com`
	 *
	 * @var string
	 */
	private $host;

	/**
	 * The path is added to the end of the host
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Default is 443 but should be changed by whatever is passed in through the Adapter.
	 *
	 * @var integer
	 */
	private $port;

	/**
	 * @var Adapter
	 */
	private $adapter;

    /**
     * Name of network, either "livenet" or "testnet"
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Bitcoin network address version
     *
     * @return string
     */
    public function getAddressVersion()
    {
        return $this->addrVersion;
    }

    /**
     * The URI used to interact with this gateway
     *
     * @return string
     */
    public function getApiHost()
    {
        return $this->hostURL;
    }

    /**
     * The port used to interact with this gateway
     *
     * @return integer
     */
    public function getApiPort()
    {
        return $this->hostPort;
    }

    /**
     * Checks to see if this is a valid HTTP method.
     *
     * @return bool
     */
    public function isMethod($method)
    {
    	return (strtoupper($method) == strtoupper($this->method));
    }

    /**
     * @inheritdoc
     */
    public function getPort()
    {
    	return $this->port;
    }

    /**
     * This is called in the Adapter
     *
     * @inheritdoc
     */
    public function setPort($port)
    {
    	$this->port = $port;
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
    	return $this->method;
    }

    /**
     * Set the method of the request, for known methods see the
     * RequestInterface
     *
     * @param string $method
     */
    public function setMethod($method)
    {
    	$this->method = $method;
    }

    /**
     * @inheritdoc
     */
    public function getSchema()
    {
    	return $this->schema;
    }

    /**
     * @inheritdoc
     */
    public function setSchema($schema)
    {
    	return $this->schema = $schema;
    }

    /**
     * @inheritdoc
     */
    public function getUri()
    {
    	return sprintf(
    			'%s://%s/%s',
    			$this->getSchema(),
    			$this->getHost(),
    			$this->getPath()
    	);
    }

    /**
     * @inheritdoc
     */
    public function getUriWithPort()
    {
    	return sprintf(
    			'%s://%s:%s/%s',
    			$this->getSchema(),
    			$this->getHost(),
    			$this->getPort(),
    			$this->getPath()
    	);
    }

    /**
     * @inheritdoc
     */
    public function getHost()
    {
    	return $this->host;
    }

    /**
     * Sets the host for the request
     *
     * @param string $host
     */
    public function setHost($host)
    {
    	$this->host = $host;

    	return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatusCode()
    {
    	return $this->statusCode;
    }

    /**
     * @param integer
     *
     * @return ResponseInterface
     */
    public function setStatusCode($statusCode)
    {
    	$this->statusCode = (integer) $statusCode;

    	return $this;
    }

    /**
     * Returns the body of the gateway response.
     *
     * @return mixed
     */
    public function getBody()
    {
    	return $this->body;
    }

    /**
     * Set the body of the response
     *
     * @param string $body
     */
    public function setBody($body)
    {
    	$this->body = $body;

    	return $this;
    }

    /**
     * Returns an array of valid headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        // remove invalid headers
        $headers = $this->headers;
        foreach ($headers as $header => $value) {
            if (empty($header) || empty($value)) {
                unset($headers[$header]);
            }
        }

        return $headers;
    }

    /**
     * @param string $header
     * @param string $value
     */
    public function setHeader($header, $value)
    {
    	$this->headers[$header] = $value;

    	return $this;
    }

    /**
     * @return string
     */
    public function getHeadersAsString()
    {
    	$headers = $this->getHeaders();
    	$return  = '';

    	foreach ($headers as $h => $v) {
    		$return .= sprintf("%s: %s\r\n", $h, $v);
    	}

    	return $return."\r\n";
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
    	return $this->path;
    }

    /**
     * @param string $host
     */
    public function setPath($path)
    {
    	$this->path = $path;

    	return $this;
    }

    public function getHeaderFields()
    {
    	$fields = array();
    	foreach ($this->getHeaders() as $header => $value) {
    		$fields[] = sprintf('%s: %s', $header, $value);
    	}

    	return $fields;
    }

    /**
     * Returns a nonce for use in REST calls.
     *
     * @see http://en.wikipedia.org/wiki/Cryptographic_nonce
     *
     * @return string
     */
    public static function nonce()
    {
    	return microtime(true);
    }

    /**
     * Returns a GUID for use in REST calls.
     *
     * @see http://en.wikipedia.org/wiki/Globally_unique_identifier
     *
     * @return string
     */
    public static function guid()
    {
    	return sprintf(
    			'%s-%s-%s-%s-%s',
    			bin2hex(openssl_random_pseudo_bytes(4)),
    			bin2hex(openssl_random_pseudo_bytes(2)),
    			bin2hex(openssl_random_pseudo_bytes(2)),
    			bin2hex(openssl_random_pseudo_bytes(2)),
    			bin2hex(openssl_random_pseudo_bytes(6))
    	);
    }
}
