<?php

namespace Floip\Tests\Evaluator;

use PHPUnit\Framework\TestCase;
use Floip\Evaluator\LogicEvaluator;
use Floip\Evaluator\Node;

class LogicEvaluatorTest extends TestCase
{
    /** @var LogicEvaluator */
    private $evaluator;

    public function setUp()
    {
        $this->evaluator = new LogicEvaluator;
    }

    /**
     * @dataProvider logicProvider
     */
    public function testLogicalOperations(array $node, $expected)
    {
        $result = $this->evaluator->evaluate(new Node($node), []);

        $this->assertEquals($expected, $result);
    }

    public function logicProvider()
    {
        return [
            [
                [
                    'type' => 'LOGIC',
                    'lhs' => '3',
                    'rhs' => '2',
                    'operator' => '>'
                ],
                true
            ],
            [
                [
                    'type' => 'LOGIC',
                    'lhs' => '3',
                    'rhs' => '2',
                    'operator' => '<'
                ],
                false
            ],
            [
                [
                    'type' => 'LOGIC',
                    'lhs' => '3',
                    'rhs' => '2',
                    'operator' => '<='
                ],
                false
            ],
            [
                [
                    'type' => 'LOGIC',
                    'lhs' => '3',
                    'rhs' => '3',
                    'operator' => '<='
                ],
                true
            ],
            [
                [
                    'type' => 'LOGIC',
                    'lhs' => '3',
                    'rhs' => '2',
                    'operator' => '>='
                ],
                true
            ],
            [
                [
                    'type' => 'LOGIC',
                    'lhs' => '3',
                    'rhs' => '3',
                    'operator' => '>='
                ],
                true
            ],
            [
                [
                    'type' => 'LOGIC',
                    'lhs' => '2',
                    'rhs' => '2',
                    'operator' => '='
                ],
                true
            ],
        ];
    }
}
