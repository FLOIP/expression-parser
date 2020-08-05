<?php

namespace Viamo\Floip\Tests\Evaluator\MethodHandler;

use PHPUnit\Framework\TestCase;
use Viamo\Floip\Evaluator\Node;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\ArrayHandler;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\ArrayHandler as ArrayHandlerContract;

class ArrayHandlerTest extends TestCase
{
    /** @var ArrayHandlerContract */
    private $arrayHandler;

    public function setUp()
    {
        $this->arrayHandler = new ArrayHandler;
    }

    public function testConstructsArray() {
        $expected = ['abc', '123'];
        $actual = call_user_func_array([$this->arrayHandler, 'array'], $expected);

        $this->assertEquals($expected, $actual);
    }

    public function testConstructsArrayFromNodes() {
        $node1 = new Node([]);
        $node2 = new Node([]);
        $node1->setValue('abc');
        $node2->setValue('123');

        $expected = ['abc', '123'];
        $actual = call_user_func_array([$this->arrayHandler, 'array'], [$node1, $node2]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider inProvider
     */
    public function testIn($value, $array, $expected) {
        $search = new Node([]);
        $search->setValue($array);

        $actual = $this->arrayHandler->in($value, $search);

        $this->assertEquals($expected, $actual);
    }

    public function inProvider() {
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
	public function testCount($array, $expected) {
		$search = new Node([]);
		$search->setValue($array);

		$actual = $this->arrayHandler->count($search);

		$this->assertEquals($expected, $actual);
    }

	public function countProvider() {
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
