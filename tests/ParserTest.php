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

    private function buildLocation(array $start, array $end)
    {
        $keys = ['offset', 'line', 'column'];
        $start = array_combine($keys, $start);
        $end = array_combine($keys, $end);
        return compact('start', 'end');
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

    /**
     * @dataProvider multipleExpressionProvider
     */
    public function testParserParsesMultipleExpressions($string, $expectedCount)
    {
        $ast = $this->parser->parse($string);
        $this->assertEquals($expectedCount, count($ast));
    }

    /**
     * @dataProvider nestedFunctionProvider
     */
    public function testFunctionsCanHaveExpressionsAsArguments($string, $expectedExpressionArgs)
    {
        $ast = $this->parser->parse($string);
        $node = $ast[0];
        $args = $node['args'];
        $this->assertEquals($expectedExpressionArgs, count($args));
    }

    /**
     * @dataProvider simpleMathProvider
     */
    public function testParserParsesSimpleMathExpression($string, $lhs, $rhs, $operator, array $location)
    {
        $ast = $this->parser->parse($string);
        $this->assertNotEmpty($ast);
        $node = $ast[0];
        $expected = ['type' => 'MATH', 'lhs' => $lhs, 'rhs' => $rhs, 'operator' => $operator, 'location' => $location];
        $this->assertArraySubset($expected, $node);
    }

    /**
     * @dataProvider mathWithExpressionOperandsProvider
     */
    public function testParserParsesMathWithExpressionsAsOperands($string, $lhs, $rhs, $operator)
    {
        $ast = $this->parser->parse($string);
        $node = $ast[0];
        $expected = compact('lhs', 'rhs');
        $this->assertArraySubset($expected, $node);
    }

    /**
     * @dataProvider simpleLogicProvider
     */
    public function testParserParsesSimpleLogicExpression($string, $lhs, $rhs, $operator, array $location)
    {
        $ast = $this->parser->parse($string);
        $this->assertNotEmpty($ast);
        $node = $ast[0];
        $expected = ['type' => 'LOGIC', 'lhs' => $lhs, 'rhs' => $rhs, 'operator' => $operator, 'location' => $location];
        $this->assertArraySubset($expected, $node);
    }

    /**
     * @dataProvider logicWithExpressionOperandsProvider
     */
    public function testParserParsesLogicWithExpressionsAsOperands($string, $lhs, $rhs, $operator)
    {
        $ast = $this->parser->parse($string);
        $node = $ast[0];
        $expected = compact('lhs', 'rhs');
        $this->assertArraySubset($expected, $node);        
    }

    public function plainStringProvider()
    {
        return [
            ['Hello World!'],
            ['1 + 2 is math'],
            ['Hey (This is in parens).'],
            ['Greater > Than'],
            ['This contact.name looks like an expression']
        ];
    }

    public function simpleMemberAccessProvider()
    {
        // last element is location start/end: offset / line / column
        return [
            ['Hello @contact.name', 'contact', 'name', $this->buildLocation([6, 1, 7], [19, 1, 20])],
            ['Hello @contact', 'contact', null, $this->buildLocation([6, 1, 7], [14, 1, 15])],
            ['@person.lastname you are special', 'person', 'lastname', $this->buildLocation([0, 1, 1], [16, 1, 17])],
            ['Hello @(contact.name)', 'contact', 'name', $this->buildLocation([6, 1, 7], [21, 1, 22])],
            ['Hello @(contact)', 'contact', null, $this->buildLocation([6, 1, 7], [16, 1, 17])],
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
            ['The date is @(NOW())', 'NOW', [], $this->buildLocation([12, 1, 13], [20, 1, 21])],
            ['@(DATE(2012, 12, 25)) was a holiday.', 'DATE', [2012, 12, 25], $this->buildLocation([0, 1, 1], [21, 1, 22])],
            ['This @(upper("hello")) is a function.', 'upper', ["hello"], $this->buildLocation([5, 1, 6], [22, 1, 23])],
        ];
    }

    public function multipleExpressionProvider()
    {
        // last element is number of expected expressions (AST nodes)
        return [
            ['Hello @contact.name today is @(NOW())', 2],
            ['We can be reached at @organization.phone except on @(DATE(2012, 12, 24)) or @(DATE(2012, 12, 25))', 3]
        ];
    }

    public function nestedFunctionProvider()
    {
        // 2nd param is expected the number of expressions as arguments
        return [
            ['Your name is @(UPPER(contact.name))', 1],
            ['Your full name is @(UPPER(contact.firstname, contact.lastname))', 2],
            ['The sum is @(SUM(contact.age, MIN(12, contact.age))', 2],
            ['The sum is @(SUM(2 + 2, contact.age, 42 / 6, 8 * 2))', 4]
        ];
    }

    public function simpleMathProvider()
    {
        // after string, params are lhs, rhs, operator
        // last param is location: offset / line / column
        return [
            ['Some math is @(1 + 2)', 1, 2, '+', $this->buildLocation([13, 1, 14], [21, 1, 22])],
            ['@(4 - 3) no spaces', 4, 3, '-', $this->buildLocation([0, 1, 1], [8, 1, 9])],
            ['@(6 / 2) is three', 6, 2, '/', $this->buildLocation([0, 1, 1], [8, 1, 9])],
            ['@(7 * 77) is hard', 7, 77, '*', $this->buildLocation([0, 1, 1], [9, 1, 10])],
        ];
    }

    public function mathWithExpressionOperandsProvider()
    {
        // lhs, rhs, operator
        return [
            ['@(some.number - other.number) is a number', ['type' => 'MEMBER'], ['type' => 'MEMBER'], '-'],
            ['@(SOMEFUNC() + OTHERFUNC())', ['type' => 'METHOD'], ['type' => 'METHOD'], '+'],
            ['@(SOMEFUNC() / some.member)', ['type' => 'METHOD'], ['type' => 'MEMBER'], '/'],
        ];
    }

    public function simpleLogicProvider()
    {
        // after string, params are lhs, rhs, operator
        // last param is location: offset / line / column
        return [
            ['Some logic is @(1 < 2)', 1, 2, '<', $this->buildLocation([14, 1, 15], [22, 1, 23])],
            ['@(0 = 0) no spaces', 0, 0, '=', $this->buildLocation([0, 1, 1], [8, 1, 9])],
            ['@(6 >= 2) is true', 6, 2, '>=', $this->buildLocation([0, 1, 1], [9, 1, 10])],
            ['@(7 <= 77) is false', 7, 77, '<=', $this->buildLocation([0, 1, 1], [10, 1, 11])],
        ];        
    }

    public function logicWithExpressionOperandsProvider()
    {
        // lhs, rhs, operator
        return [
            ['@(some.number > other.number) is a number', ['type' => 'MEMBER'], ['type' => 'MEMBER'], '-'],
            ['@(SOMEFUNC() = OTHERFUNC())', ['type' => 'METHOD'], ['type' => 'METHOD'], '+'],
            ['@(SOMEFUNC() <= some.member)', ['type' => 'METHOD'], ['type' => 'MEMBER'], '/'],
        ];
    }
}
