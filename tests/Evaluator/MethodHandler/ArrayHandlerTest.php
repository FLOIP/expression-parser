<?php

namespace Viamo\Floip\Tests\Evaluator\MethodHandler;

use Viamo\Floip\Tests\TestCase;
use Viamo\Floip\Evaluator\Node;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\ArrayHandler;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\ArrayHandler as ArrayHandlerContract;
use function call_user_func_array;

class ArrayHandlerTest extends TestCase
{
    /** @var ArrayHandlerContract */
    private ArrayHandlerContract|ArrayHandler $arrayHandler;

    public function setUp(): void
    {
        $this->arrayHandler = new ArrayHandler;
        parent::setUp();
    }

    public function testConstructsArray(): void {
        $expected = ['abc', '123'];
        $actual = call_user_func_array([$this->arrayHandler, 'array'], $expected);

        $this->assertEquals($expected, $actual);
    }

    public function testConstructsArrayFromNodes(): void {
        $node1 = new Node([]);
        $node2 = new Node([]);
        $node1->setValue('abc');
        $node2->setValue('123');

        $expected = ['abc', '123'];
        $actual = call_user_func_array([$this->arrayHandler, 'array'], [$node1, $node2]);

        $this->assertEquals($expected, $actual);
    }

    public function testTrimsContextFromArgs(): void {
        $expected = [1, 2, 3];
        $actual = call_user_func_array([$this->arrayHandler, 'array'], [1, 2, 3]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider inProvider
     */
    public function testIn($value, $array, $expected): void {
        $search = new Node([]);
        $search->setValue($array);

        $actual = $this->arrayHandler->in($value, $search);

        $this->assertEquals($expected, $actual);
    }

    public function inProvider(): array {
        return [
            [
                'world',
                ['hello', 'world', 'foo'],
                true
            ],
            [
                'world',
                ['hello', 'foo', 'bar'],
                false
            ]
        ];
    }

    /**
     * @param $array
     * @param $expected
     * @dataProvider countProvider
     */
    public function testCount($array, $expected): void {
        $search = new Node([]);
        $search->setValue($array);

        $actual = $this->arrayHandler->count($search);

        $this->assertEquals($expected, $actual);
    }

    public function countProvider(): array {
        return [
            [
                [],
                0
            ],
            [
                [''],
                1
            ],
            [
                ['hello', 'foo', 'bar'],
                3
            ]
        ];
    }
}
