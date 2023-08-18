<?php

declare(strict_types=1);

namespace PwC\RouteFinder;

use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * Caching mechanism this application runtime will use.
 */
final class AppCacheEngine extends Psr16Cache
{
    public function __construct()
    {
        parent::__construct((new FilesystemAdapter()));
    }
}
