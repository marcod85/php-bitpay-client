<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * @package Bitpay
 */
class Autoloader
{
    /**
     * Register the autoloader, by default this will put the BitPay autoloader
     * first on the stack, to append the autoloader, pass `false` as an argument.
     *
     * Some applications will throw exceptions if the class isn't found and
     * some are not compatible with PSR standards.
     *
     * @param boolean $prepend
     */
    public static function register($prepend = true)
    {
        if (false === spl_autoload_register(array(__CLASS__, 'autoload'), true, (bool) $prepend)) {
            throw new \Exception('[ERROR] In Autoloader::register(): System call to register autoloader failed.');
        }
    }

    /**
     * Unregister this autoloader
     */
    public static function unregister()
    {
        if (false === spl_autoload_unregister(array(__CLASS__, 'autoload'))) {
            throw new \Exception('[ERROR] In Autoloader::unregister(): System call to unregister autoloader failed.');
        }
    }

    /**
     * Give a class name and it will require the file.
     *
     * @param  string $class
     * @return bool
     */
    public static function autoload($class)
    {
        if (false === isset($class) || true === empty($class)) {
            throw new \Exception('[ERROR] In Autoloader::autoload(): Missing or invalid $class parameter.');
        }

        $isBitpay = false;
        $upLevl   = DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

        if (0 === strpos($class, 'Bitpay\\')) {
            $isBitpay = true;
        }

        $file = __DIR__ . $upLevl . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

        if (true === is_file($file) && true === is_readable($file)) {
            require_once $file;

            return true;
        }

        /**
         * Only want to throw exceptions if class is under Bitpay namespace
         */
        if ($isBitpay) {
            throw new \Exception('[ERROR] In Autoloader::autoload(): Class "' . $class . '" not found or class file is unreadable.');
        }
    }
}
