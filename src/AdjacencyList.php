<?php

declare(strict_types=1);

namespace PwC\RouteFinder;

/**
 * Provides graph structure query interface.
 */
final class AdjacencyList
{
    private array $list = [];
    private array $visited = [];

    public function link(string $a, string $b): void
    {
        if (array_key_exists($a, $this->list)) {
            $this->list[$a][] = $b;
        } else {
            $this->list[$a] = [$b];
        }
    }

    private function dfs(string $networkId, string $needle, array $carry = []): array
    {
        $network = $this->list[$networkId] ?? [];
        $this->visited[$networkId] = true;

        if (in_array($needle, $network)) {
            return [...$carry, $networkId, $needle];
        }

        foreach ($network as $node) {
            if (!array_key_exists($node, $this->visited)) {
                $result = $this->dfs($node, $needle, [...$carry, $networkId]);
                if (end($result) === $needle) {
                    return $result;
                }
            }
        }

        return $carry;
    }

    public function findPath(string $a, string $b): array
    {
        // On averge the tree structure form country perspective will be rather shallow,
        // since there are 100 times more countries than directly bordering countrie.
        // Given this branching factor and the fact that ANY FIRST existing path
        // should be returned, DFS algorithm is chosen in favor of BFS.
        //
        // (i) If need be more efficient, implement and invoke 
        // the Bidirectional swarm algorithm, here.
        //
        // (!) Since this is a homework, I will pass on that unclear detail ;)
        return $this->dfs($a, $b);
    }
}
