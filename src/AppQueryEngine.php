<?php

declare(strict_types=1);

namespace PwC\RouteFinder;

use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use PwC\RouteFinder\AdjacencyList;

class AppQueryEngine
{
    public function __construct(
        private CountriesApiClient $api,
        private CacheInterface $cache,
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    /**
     * @param string $origin  Country code.
     * @param string $destination  Country code.
     * @return string[]|null  List of country codes in no particular sequence if path exists, else null is returned.
     */
    public function findPath(string $origin, string $destination): ?array
    {
        $data = [];
        $key = '[EngineV1][PathfindV1][InputV1' . $origin . $destination . ']';
        if (!$this->cache->has($key)) {
            $this->logger->warning('CACHE MISS: ' . $key);
            $data = $this->api->listBorderingCodePairs();

            $result = self::mkAdjList($data)->findPath($origin, $destination);

            $this->cache->set($key, $result);
        }

        return $this->cache->get($key);
    }

    public static function mkAdjList(array $data): AdjacencyList
    {
        $adjList = new AdjacencyList();

        foreach ($data as [$left, $right]) {
            $adjList->link($left, $right);
        }

        return $adjList;
    }
}
