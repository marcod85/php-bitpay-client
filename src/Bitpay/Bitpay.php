<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

use Bitpay\DependencyInjection\BitpayExtension;
use Bitpay\DependencyInjection\Loader\ArrayLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Setups container and is ready for some dependency injection action
 *
 * @package Bitpay
 */
class Bitpay
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * First argument can either be a string or fullpath to a yaml file that
     * contains configuration parameters. For a list of configuration values
     * see \Bitpay\Config\Configuration class
     *
     * The second argument is the container if you want to build one by hand.
     *
     * @param array|string       $config
     * @param ContainerInterface $container
     */
    public function __construct($config = array(), ContainerInterface $container = null)
    {
        $this->container = $container;

        if (true === is_null($container)) {
            $this->initializeContainer($config);
        }
    }

    /**
     * Initialize the container
     *
     * @param mixed $config
     */
    protected function initializeContainer($config)
    {
        $this->container = $this->buildContainer($config);
        
        if (true === isset($this->container) && false === empty($this->container)) {
            $this->container->compile();
        } else {
            throw new \Exception('[ERROR] In Bitpay::initializeContainer(): Could not initialize the container object.');
        }
    }

    /**
     * Build the container of services and parameters
     *
     * @return object $container
     */
    protected function buildContainer($config)
    {
        $container = new ContainerBuilder(new ParameterBag($this->getParameters()));

        if (true === isset($container) && false === empty($container)) {
            $this->prepareContainer($container);
            $this->getContainerLoader($container)->load($config);
        } else {
            throw new \Exception('[ERROR] In Bitpay::buildContainer(): Could not initialize the container object.');
        }

        return $container;
    }

    /**
     * Returns the absolute root directory path for this project.
     *
     * @return array
     */
    protected function getParameters()
    {
        return array('bitpay.root_dir' => realpath(__DIR__.'/..'),);
    }

    /**
     */
    private function prepareContainer(ContainerInterface $container)
    {
        if (true === isset($container) && false === empty($container)) {
            foreach ($this->getDefaultExtensions() as $ext) {
                $container->registerExtension($ext);
                $container->loadFromExtension($ext->getAlias());
            }
        } else {
            throw new \Exception('[ERROR] In Bitpay::prepareContainer(): Missing or invalid $container parameter.');
        }
    }

    /**
     * @param  ContainerInterface $container
     * @return LoaderInterface
     */
    private function getContainerLoader(ContainerInterface $container)
    {
        if (true === isset($container) && false === empty($container)) {
            $locator  = new FileLocator();

            $resolver = new LoaderResolver(
                array(
                    new ArrayLoader($container),
                    new YamlFileLoader($container, $locator),
                )
            );

            return new DelegatingLoader($resolver);

        } else {
            throw new \Exception('[ERROR] In Bitpay::getContainerLoader(): Missing or invalid $container parameter.');
        }
    }

    /**
     * Returns an array of the default extensions
     *
     * @return array
     */
    private function getDefaultExtensions()
    {
        return array(new BitpayExtension(),);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return mixed
     */
    public function get($service)
    {
        if (true === isset($container) && false === empty($container)) {
            return $this->container->get($service);
        } else {
            throw new \Exception('[ERROR] In Bitpay::get(): Could not get $service because the $container object is missing or invalid.');
        }
    }
}
