<?php
/**
 * User: Ryan Castle <ryan@dwd.com.au>
 * Date: 4/06/14
 */

require_once __DIR__ . '/vendor/Composer/ClassLoader.php';

$loader = new \Composer\Autoload\ClassLoader();

$vendorDir = __DIR__ . '/vendor';
$baseDir = dirname($vendorDir);

$map = array(
    'cURL\\Tests' => array($vendorDir . '/stil/curl-easy/tests'),
    'cURL' => array($vendorDir . '/stil/curl-easy/src'),
    'Symfony\\Component\\EventDispatcher\\' => array($vendorDir . '/symfony/event-dispatcher'),
    'Checker' => array($baseDir .'/src'),
);

foreach ($map as $namespace => $path) {
    $loader->set($namespace, $path);
}

$loader->register(true);

