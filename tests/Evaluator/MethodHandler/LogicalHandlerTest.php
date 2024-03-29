<?php

namespace Viamo\Floip\Tests\Evaluator\MethodHandler;

use Viamo\Floip\Tests\TestCase;
use Viamo\Floip\Evaluator\Node;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Logical;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Logical as LogicalContract;

class LogicalHandlerTest extends TestCase
{
    /** @var LogicalContract */
    private Logical|LogicalContract $logical;

    public function setUp(): void
    {
        $this->logical = new Logical;
        parent::setUp();
    }

    /**
     * @dataProvider andProvider
     */
    public function testAnd(array $args, $expected): void {
        $this->assertEquals($expected, call_user_func_array([$this->logical, 'and'], $args));
    }

    /**
     * @dataProvider ifProvider
     */
    public function testIf(array $args, $expected): void {
        $this->assertEquals($expected, call_user_func_array([$this->logical, 'if'], $args));
    }

    /**
     * @dataProvider orProvider
     */
    public function testOr(array $args, $expected): void {
        $this->assertEquals($expected, call_user_func_array([$this->logical, 'or'], $args));
    }

    private function makeNode($value): Node {
        $node = new Node([]);
        $node->setValue(($value));
        return $node;
    }

    public function ifProvider(): array {
        return [
            [[true, 1, 2], 1],
            [[false, 1, 2], 2],
            [[$this->makeNode(true), 1, 2], 1],
            [[$this->makeNode(false), 1, 2], 2],
            [[$this->makeNode('TRUE'), 1, 2], 1],
            [[$this->makeNode('FALSE'), 1, 2], 2]
        ];
    }

    public function andProvider(): array {
        return [
            [[true, true], true],
            [[true, false], false],
            [[true, true, false], false],
            [[$this->makeNode(true), true], true],
            [[$this->makeNode(false), true], false],
            [[$this->makeNode('TRUE'), true], true],
            [[$this->makeNode('FALSE'), true], false],
            [['TRUE', 'TRUE'], true],
            [[$this->makeNode('TRUE'), $this->makeNode('TRUE')], true],
        ];
    }

    public function orProvider(): array {
        return [
            [[true, false], true],
            [[false, true], true],
            [[false, false, true], true],
            [[false, false], false],
            [[$this->makeNode(true), false], true],
            [[$this->makeNode(false), true], true],
            [[$this->makeNode(false), false], false]
        ];
    }
}
