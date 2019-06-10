<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Floip\Evaluator\Contract\Excellent as ExcellentInterface;
use Floip\Evaluator\Excellent;

class ExcellentTest extends TestCase
{
    /**
     * @var ExcellentInterface
     */
    private $excellent;

    public function setUp()
    {
        $this->excellent = new Excellent;
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
     * @dataProvider wordPuncProvider
     */
    public function testWordPunc($input, $number, $expected)
    {
        $this->assertEquals($expected, $this->excellent->word($input, $number));
    }

    /**
     * @dataProvider wordSpaceProvider
     */
    public function testWordSpace($input, $number, $expected)
    {
        $this->assertEquals($expected, $this->excellent->word($input, $number, true));
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

    public function wordPuncProvider()
    {
        return [
            ['Foo,Bar,Baz', 1, 'Foo'],
            ['Foo,Bar,Baz', 2, 'Bar'],
            ['FooBar Bar,Foo, Baz', 3, 'Foo'],
            ['One-Two.Three!Four;Five:Six?Seven Eight', 7, 'Seven'],
            ['One.Two-Three,Four', -1, 'Four'],
            ['One,Two-Three,Four', -2, 'Three'],
        ];
    }

    public function wordSpaceProvider()
    {
        return [
            ['Foo,Bar Baz, FooBar', 2, 'Baz'],
            ['Foo Baz,Foo, Foobar', 3, 'Foobar'],
            ['Foo Baz Bar Foobar', -2, 'Bar'],
        ];
    }
}
