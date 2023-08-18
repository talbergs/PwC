<?php

declare(strict_types=1);

namespace PwC\RouteFinder;

use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Client and 3rd party resource API wrapper (acl) in one file.
 * If need be, request retries are to be implemented here or in HTTP client.
 * For now unhappy path is handled at very edge of application (at ./index.php).
 */
final class CountriesApiClient
{
    public function __construct(
        private CacheInterface $cache,
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    /**
     * @return string[][]  List of tuples of codes that has mutual link.
     */
    public function listBorderingCodePairs(): array
    {
        $url = 'https://raw.githubusercontent.com/mledoze/countries/master/countries.json';
        $key = '[ApiV1][CodePairsV1][InputV1]';

        if (!$this->cache->has($key)) {
            $this->cache->set($key, $this->request($url));
        }

        $pairs = [];
        foreach ($this->cache->get($key) as ['cca3' => $leftCode, 'borders' => $rightCodes]) {
            foreach ($rightCodes as $rightCode) {
                $pairs[] = [$leftCode, $rightCode];
            }
        }

        return $pairs;
    }

    private function request(string $url): array
    {
        $this->logger->warning(__METHOD__ . ' URL: ' . $url);

        return json_decode(file_get_contents($url), true);
    }
}
