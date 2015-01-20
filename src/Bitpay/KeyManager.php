<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * Used to manage keys
 *
 * @package Bitpay
 */
class KeyManager
{
    /**
     * @var Bitpay\Storage\StorageInterface
     */
    protected $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(\Bitpay\Storage\StorageInterface $storage)
    {
        if (true === isset($storage) && false === empty($storage)) {
            $this->storage = $storage;
        } else {
            throw new \Exception('[ERROR] In KeyManager::__construct(): Missing or invalid $storage parameter.');
        }
    }

    /**
     * @param KeyInterface $key
     */
    public function persist(KeyInterface $key)
    {
        if (true === isset($key) && false === empty($key)) {
            $this->storage->persist($key);
        } else {
            throw new \Exception('[ERROR] In KeyManager::persist(): Missing or invalid $key parameter.');
        }
    }

    /**
     * @return KeyInterface
     */
    public function load($id)
    {
        if (true === isset($id) && false === empty($id)) {
            return $this->storage->load($id);
        } else {
            throw new \Exception('[ERROR] In KeyManager::load(): Missing or invalid $id parameter.');
        }
    }
}
