<?php

namespace Viamo\Floip\Tests\Evaluator;

use Viamo\Floip\Tests\TestCase;
use Viamo\Floip\Evaluator\Node;
use Viamo\Floip\Evaluator\MathNodeEvaluator;

class MathEvaluatorTest extends TestCase
{
    /** @var MathNodeEvaluator */
    protected $evaluator;

    public function setUp(): void
    {
        $this->evaluator = new MathNodeEvaluator;
        parent::setUp();
    }

    /**
     * @dataProvider mathProvider
     */
    public function testMathOperations(array $node, $expected)
    {
        $result = $this->evaluator->evaluate(new Node($node), []);

        $this->assertEquals($expected, $result);
    }

    public function mathProvider()
    {
        return [
            [
                [
                    'type' => 'MATH',
                    'lhs' => '3',
                    'rhs' => '2',
                    'operator' => '+'
                ],
                '5'
            ],
            [
                [
                    'type' => 'MATH',
                    'lhs' => '3',
                    'rhs' => '2',
                    'operator' => '-',
                ],
                '1'
            ],
            [
                [
                    'type' => 'MATH',
                    'lhs' => '16',
                    'rhs' => '2',
                    'operator' => '/'
                ],
                '8'
            ],
            [
                [
                    'type' => 'MATH',
                    'lhs' => '6',
                    'rhs' => '7',
                    'operator' => '*'
                ],
                '42',
            ],
            [
                [
                    'type' => 'MATH',
                    'lhs' => '3',
                    'rhs' => '4',
                    'operator' => '^'
                ],
                '81',
            ]
        ];
    }
}
