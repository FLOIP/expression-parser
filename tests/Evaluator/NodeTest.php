<?php

namespace Viamo\Floip\Tests\Evaluator;

use PHPUnit\Framework\TestCase;
use Viamo\Floip\Evaluator\Node;

class NodeTest extends TestCase
{
    public function testConstructsNestedNodes()
    {
        $rawNode = [
            'type' => 'METHOD',
            'args' => [
                [
                    'type' => 'METHOD',
                    'args' => [
                        'foobar',
                    ]
                ],
                'bar'
            ]
        ];
        $node = new Node($rawNode);

        $this->assertInstanceOf(Node::class, $node['args'][0]);
        $this->assertEquals('foobar', $node['args'][0]['args'][0]);
        $this->assertEquals('bar', $node['args'][1]);
    }

    /**
     * @dataProvider nodeValuesProvider
     */
    public function testNodeValuesToString($value, $expected)
    {
        $node = new Node([]);
        $node->setValue($value);
        $this->assertEquals($expected, (string)$node);
    }

    public function nodeValuesProvider()
    {
        return [
            ['one', 'one'],
            [1, '1'],
            [1.456, '1.456'],
            [[1, 2, 3], '1, 2, 3']
        ];
    }
}
