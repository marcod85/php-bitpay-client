<?php
/**
 * Copyright (c) 2014-2015 BitPay
 */

require __DIR__ . '/../vendor/autoload.php';

/* 
 * If you're not familiar with YAML already, some good resources to get up to
 * speed are the official YAML website and the Wikipedia article on the subject.
 * See: http://www.yaml.org/  and  http://en.wikipedia.org/wiki/YAML
 *
 * YAML syntax was designed to be easily mapped to data types common to most
 * high-level languages: list, associative array, and scalar.  Its familiar
 * indented outline and lean appearance make it especially suited for tasks
 * where humans are likely to view or edit data structures, such as configuration
 * files.  This is what we are using YAML for in this library: configuration.
 *
 * To handle these types of config files, we make use of the YamlFileLoader
 * provided by Symfony\Component\DependencyInjection\Loader\YamlFileLoader.
 * However, you are free to use your own as long as you modifiy the corresponding
 * dependency injection code in the Bitpay.php file.
 *
 * To use a YAML config file with this library, just pass in the path to the yml
 * file, like the example below.  Note the filename extension is ".yml".
 */

$bitpay = new \Bitpay\Bitpay(__DIR__ . '/config.yml');
