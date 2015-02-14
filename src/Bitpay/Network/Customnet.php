<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Network;

/**
 * Custom networking class that's more configurable for
 * testing and/or using any non-standard parameters.
 *
 * @package Bitpay
 */
class Customnet implements Network
{
	/**
	 * @var string
	 */
    private $host_url;

    /**
     * @var int
     */
    private $host_port;

    /**
     * @var bool
     */
    private $isPortRequiredInUrl;

    /**
     * Public constructor method to initialize class properties.
     *
     * @param string  $url
     * @param int     $port
     * @param bool    $isPortRequiredInUrl
     */
    public function __construct($url, $port = 443, $isPortRequiredInUrl = false)
    {
    	if (true === isset($url)) {
            $this->host_url  = trim($url);
    	}

        $this->host_port = $port;

        $this->isPortRequiredInUrl = $isPortRequiredInUrl;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Custom Network';
    }

    /**
     * @inheritdoc
     */
    public function getAddressVersion()
    {
        return self::LIVENET_ADDR;
    }

    /**
     * @inheritdoc
     */
    public function getApiHost()
    {
        return $this->host_url;
    }

    /**
     * @inheritdoc
     */
    public function getApiPort()
    {
        return $this->host_port;
    }
}
