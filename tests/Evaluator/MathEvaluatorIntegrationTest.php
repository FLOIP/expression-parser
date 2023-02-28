<?php

namespace Viamo\Floip\Tests\Evaluator;

use Carbon\Carbon;
use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\Node;
use Viamo\Floip\Evaluator\MathNodeEvaluator;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\DateTime;
use Viamo\Floip\Tests\TestCase;

class MathEvaluatorIntegrationTest extends TestCase
{
    /** @var MathNodeEvaluator */
    protected MathNodeEvaluator $mathEvaluator;

    public function setUp(): void
    {
        $this->mathEvaluator = new MathNodeEvaluator;
        parent::setUp();
    }

    /**
     * @dataProvider dateTimeMathProvider
     */
    public function testMathOperatesOnDatesAndTimes(array $node, $expected)
    {
        $result = $this->mathEvaluator->evaluate(new Node($node), []);
        $this->assertEquals($expected, $result);
    }

    private function dateNode($year, $month, $day): Node {
        $date = (new DateTime)->date($year, $month, $day);
        return (new Node([]))->setValue($date);
    }

    private function timeNode($hour, $minute, $second): Node {
        $time = (new DateTime)->time($hour, $minute, $second);
        return (new Node([]))->setValue($time);
    }

    public function dateTimeMathProvider(): array {
        return [
            [
                [
                    'type' => ParsesFloip::MATH_TYPE,
                    'lhs' => $this->dateNode(2019, 01, 01),
                    'rhs' => $this->timeNode(9, 30, 42),
                    'operator' => '+'
                ], Carbon::parse('2019-01-01 09:30:42'),
            ],
            [
                [
                    'type' => ParsesFloip::MATH_TYPE,
                    'lhs' => $this->dateNode(2019, 01, 01),
                    'rhs' => $this->timeNode(9, 30, 42),
                    'operator' => '-'
                ], Carbon::parse('2018-12-31 14:29:18'),
            ]
        ];
    }
}
