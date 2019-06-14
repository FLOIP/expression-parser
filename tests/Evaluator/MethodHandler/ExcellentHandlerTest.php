<?php

namespace Floip\Tests\Evaluator\MethodHandler;

use PHPUnit\Framework\TestCase;
use Floip\Evaluator\MethodEvaluator\Excellent;
use Floip\Evaluator\MethodEvaluator\Contract\Excellent as ExcellentContract;

class ExcellentHandlerTest extends TestCase
{
    /** @var ExcellentContract */
    private $excellent;

    public function setUp()
    {
        $this->excellent = new Excellent;
    }

    /**
     * @dataProvider wordProvider
     */
    public function testWord($string, $number, $bySpaces, $expected)
    {
        $this->assertEquals($expected, $this->excellent->word($string, $number, $bySpaces));
    }

        /**
     * @dataProvider firstWordProvider
     */
    public function testFirstWord($input, $expected)
    {
        $this->assertEquals($expected, $this->excellent->firstWord($input));
    }

    /**
     * @dataProvider wordCountProvider
     */
    public function testWordCount($input, $expected)
    {
        $this->assertEquals($expected, $this->excellent->wordCount($input));
    }

    /**
     * @dataProvider wordSliceProvider
     */
    public function testWordSlice($string, $start, $stop, $bySpaces, $expected)
    {
        $this->assertEquals($expected, $this->excellent->wordSlice($string, $start, $stop, $bySpaces));
    }

    public function wordCountProvider()
    {
        return [
            ['I am a little teapot.', 5],
            ['I.am.a.little.teapot', 5],
            ['I am. A. Little.Teapot', 5],
            ['I.Am,A!Little;Teapot:', 5],
        ];
    }

    public function firstWordProvider()
    {
        return [
            ['Foo Bar', 'Foo'],
            ['Foo,Bar', 'Foo'],
            ['Foo. Bar', 'Foo'],
            ['Foo...bar!', 'Foo'],
        ];
    }

    public function wordProvider()
    {
        return [
            ['hello cow-boy', 2, false, 'cow'],
            ['hello cow-boy', 2, true, 'cow-boy'],
            ['hello cow-boy', -1, false, 'boy'],
        ];
    }

    public function wordSliceProvider()
    {
        return [
            ['RapidPro expressions are fun', 2, 4, null, 'expressions are'],
            ['RapidPro expressions are fun', 2, null, null, 'expressions are fun'],
            ['RapidPro expressions are fun', 1, -2, null, 'RapidPro expressions'],
            ['RapidPro expressions are fun', -1, 2, null, 'fun']
        ];
    }
}
