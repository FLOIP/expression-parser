<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Floip\Parser;

class ParserTest extends TestCase
{
    /** @var Parser */
    private $parser;

    public function setUp()
    {
        $this->parser = new Parser;
    }

    /**
     * @dataProvider plainStringProvider
     */
    public function testParserParsesPlainStringsIntoEmptyAST($string)
    {
        $result = $this->parser->parse($string);
        $expected = [0 => null];
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider simpleMemberAccessProvider
     */
    public function testParserParsesSimpleMemberAccess($string)
    {
        $result = $this->parser->parse($string);
        $this->assertNotEmpty($result);
    }

    /**
     * @dataProvider simpleMemberAccessProvider
     */
    public function testParserParsesMemberAccessStruct($string, $key, $value, array $location)
    {
        $ast = $this->parser->parse($string);
        $node = $ast[0];
        $location = array_combine(['offset', 'line', 'column'], $location);
        $expected = ['type' => 'MEMBER', 'key' => $key, 'value' => $value, 'location' => $location];

        $this->assertArraySubset($expected, $node);
    }

    /**
     * @dataProvider simpleFunctionProvider
     */
    public function testParserParsesSimpleFunc($string)
    {
        $ast = $this->parser->parse($string);
        $this->assertNotEmpty($ast);
    }

    /**
     * @dataProvider simpleFunctionProvider
     */
    public function testParserParsesFunctionStruct($string, $call, $args)
    {
        $ast = $this->parser->parse($string);
        $node = $ast[0];
        $expected = ['type' => 'METHOD', 'call' => $call, 'args' => $args];
        $this->assertArraySubset($expected, $node);
    }

    public function plainStringProvider()
    {
        return [
            ['Hello World!'],
            ['1 + 2 is math'],
            ['Hey (This is in parens).'],
            ['Greater > Than']
        ];
    }

    public function simpleMemberAccessProvider()
    {
        // location is offset / line / column
        return [
            ['Hello @contact.name', 'contact', 'name', [7, 1, 8]],
            ['Hello @contact', 'contact', null, [7, 1, 8]],
            ['@person.lastname you are special', 'person', 'lastname', [1, 1, 2]],
        ];
    }

    public function plainFuncProvider()
    {
        return [
            ['The date is @(NOW())'],
            ['@(DATE(2012, 12, 25)) was a holiday.'],
            ['This @(upper("hello")) is a function.']
        ];
    }

    public function simpleFunctionProvider()
    {
        return [
            ['The date is @(NOW())', 'NOW', [], [14, 1, 15]],
            ['@(DATE(2012, 12, 25)) was a holiday.', 'DATE', [2012, 12, 25], [1, 1, 2]],
            ['This @(upper("hello")) is a function.', 'upper', ["hello"], [7, 1, 8]],
        ];
    }
}
