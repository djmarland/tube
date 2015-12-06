<?php

use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../app/autoload.php';

$env = 'prod';
$debugMode = false;
if (true) { // @todo - detect from environment
    $env = 'dev';
    $debugMode = true;
    \Symfony\Component\Debug\Debug::enable();
}

// Enable APC for autoloading to improve performance.
// You should change the ApcClassLoader first argument to a unique prefix
// in order to prevent cache key conflicts with other applications
// also using APC.
//$apcLoader = new ApcClassLoader(sha1(__FILE__), $loader);
//$loader->unregister();
//$apcLoader->register(true);


require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

$kernel = new AppKernel($env, $debugMode);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);