<?php

namespace Viamo\Floip\Tests\Evaluator;

use Viamo\Floip\Tests\TestCase;
use Viamo\Floip\Evaluator\LogicNodeEvaluator;
use Viamo\Floip\Evaluator\Node;

class LogicNodeEvaluatorTest extends TestCase
{
    /** @var LogicNodeEvaluator */
    private $evaluator;

    public function setUp(): void
    {
        $this->evaluator = new LogicNodeEvaluator;
        parent::setUp();
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
        $tn = new Node([]);
        $tn->setValue(3);
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
            [
                [
                    'type' => 'LOGIC',
                    'lhs' => '2',
                    'rhs' => '2',
                    'operator' => '!='
                ],
                false
            ],
            [
                [
                    'type' => 'LOGIC',
                    'lhs' => '2',
                    'rhs' => '2',
                    'operator' => '<>'
                ],
                false
            ],
            [
                [
                    'type' => 'LOGIC',
                    'lhs' => $tn,
                    'rhs' => 3,
                    'operator' => '=',
                ],
                true
            ]
        ];
    }
}
