<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PwC\RouteFinder\AdjacencyList;

class AdjacencyListTest extends TestCase
{
    public static function pathFindingCases(): array
    {
        function permutations(array $vertices): array
        {
            $result = [];
            foreach ($vertices as $vertice) {
                foreach ($vertices as $verticePrim) {
                    $result[$vertice . $verticePrim] = [$vertice, $verticePrim];
                }
            }

            return array_values($result);
        }

        return [
            // Straight line.
            [
                'vertices' => [
                    ['a', 'b'],
                    ['b', 'c'],
                    ['c', 'd'],
                ],
                'query' => ['a', 'd'],
                'expect' => ['a', 'b', 'c', 'd'],
            ],
            // Straight line, no results.
            [
                'vertices' => [
                    ['a', 'b'],
                    ['b', 'c'],
                    ['c', 'd'],
                ],
                'query' => ['a', 'x'],
                'expect' => [],
            ],
            // Cyclic graph.
            [
                'vertices' => [
                    ['a', 'b'],
                    ['b', 'c'],
                    ['c', 'd'],
                    ['d', 'a'],
                ],
                'query' => ['a', 'd'],
                'expect' => ['a', 'b', 'c', 'd'],
            ],
            // Fully connected mesh.
            [
                'vertices' => permutations(['-','a', 'b', 'c', 'd']),
                'query' => ['a', 'd'],
                'expect' => ['a', 'd'],
            ],
            // Single node connected graph.
            [
                'vertices' => [['a', 'a']],
                'query' => ['a', 'a'],
                'expect' => ['a', 'a'],
            ],
            // Mixed graph #1.
            [
                'vertices' => [
                    ['a', 'b'],
                    ['a', 'c'],
                    ['x', 'y'],
                    ['x', 'x'],
                ],
                'query' => ['a', 'x'],
                'expect' => [],
            ],
            // Mixed graph #2.
            [
                'vertices' => [
                    ['a', 'b'],
                    ['a', 'c'],
                    ['x', 'y'],
                    ['x', 'x'],
                ],
                'query' => ['a', 'c'],
                'expect' => ['a', 'c'],
            ],
        ];
    }
    
    #[DataProvider('pathFindingCases')]
    public function testPathFindingCases(array $vertices, array $query, array $expect): void
    {
        $adj = new AdjacencyList();

        foreach ($vertices as [$a, $b]) {
            $adj->link($a, $b);
        }

        $result = $adj->findPath(...$query);

        $msg = json_encode(array_combine(
            keys: ['vertices', 'query', 'expect', 'result'],
            values: [...func_get_args(), $result],
        ), JSON_PRETTY_PRINT);

        $this->assertEquals($expect, $result, $msg);
    }
}
