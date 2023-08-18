<?php

declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use PwC\RouteFinder\CountriesApiClient;
use PwC\RouteFinder\AppCacheEngine;
use PwC\RouteFinder\AppQueryEngine;
use Symfony\Component\HttpKernel\Log\Logger as AppLogger;
use Symfony\Component\HttpFoundation\JsonResponse;

$input = array_values(array_filter(explode('/', $_SERVER['PATH_INFO'] ?? getenv('SERVER_PATH'))));

// If the input is accepted, here we bootstrap the whole app.
$data = [];
if (count($input) === 2) {
    $cache = new AppCacheEngine(); 
    $logger = new AppLogger(output: __DIR__ . '/app.log');
    $api = new CountriesApiClient(
        cache: $cache,
        logger: $logger,
    );

    $engine = new AppQueryEngine(api: $api, cache: $cache); 

    [$origin, $destination] = $input;
    try {
        $data = $engine->findPath($origin, $destination);
    } catch (Throwable $e) {
        $logger->critical("Not handled case, keep working.");
        $logger->error($e);
    }
}

(new JsonResponse(status: $data ? 200 : 400))->setData($data)->send();
