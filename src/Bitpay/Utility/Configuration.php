<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Utility;

/**
 * This class contains all the valid configuration settings that can be used.
 * If you update this file to add new settings, please make sure you update the
 * documentation as well.
 *
 * @see http://symfony.com/doc/current/components/config/definition.html
 *
 * @package Bitpay
 */
class Configuration
{
	/**
	 * Returns the root directory path for the
	 * main Bitpay class.
	 *
	 * @return string
	 */
	protected function getRootDirPath()
	{
		return realpath(__DIR__.'/..');
	}


    /**
     * Adds the key_storage node with validation rules
     *
     * key_storage MUST:
     *     * implement Bitpay\Storage\Storage
     *     * be a class that can be loaded
     */
    protected function addKeyStorageNode()
    {

    }
}
