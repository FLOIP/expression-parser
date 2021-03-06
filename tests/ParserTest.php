<?php

namespace Viamo\Floip\Tests;

use PHPUnit\Framework\TestCase;
use Viamo\Floip\Parser;

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
    public function testParserParsesPlainStringsIntoAST($string)
    {
        $result = $this->parser->parse($string);
        $this->assertEquals(1, count($result));
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
    public function testParserParsesMemberAccessStruct($string, array $expected)
    {
        $ast = $this->parser->parse($string);
        $this->assertTrue(true);

        $this->assertArraySubset($expected, $ast);
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
    public function testParserParsesFunctionStruct($string, array $expected)
    {
        $ast = $this->parser->parse($string);

        $this->assertArraySubset($expected, $ast);
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
    public function testFunctionsCanHaveExpressionsAsArguments($string, $methodIdx, $expectedExpressionArgs)
    {
        $ast = $this->parser->parse($string);
        $node = $ast[$methodIdx];
        $args = $node['args'];
        $this->assertEquals($expectedExpressionArgs, count($args));
    }

    /**
     * @dataProvider simpleMathProvider
     */
    public function testParserParsesSimpleMathExpression($string, array $expected)
    {
        $ast = $this->parser->parse($string);

        $this->assertArraySubset($expected, $ast);
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
    public function testParserParsesSimpleLogicExpression($string, array $expected)
    {
        $ast = $this->parser->parse($string);

        $this->assertArraySubset($expected, $ast);
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

    /**
     * @dataProvider regressionProvider
     */
    public function testExceptionRegressions($string) {
        $ast = $this->parser->parse($string);
        // an exception should be thrown on a parser regression
        $this->assertTrue(true);
    }

    private function methodNode($call, array $args)
    {
        $type = 'METHOD';
        return compact('type', 'call', 'args');
    }

    private function mathNode($lhs, $rhs, $operator)
    {
        $type = 'MATH';
        return compact('MATH', 'lhs', 'rhs', 'operator');
    }

    private function logicNode($lhs, $rhs, $operator)
    {
        $type = 'LOGIC';
        return compact('MATH', 'lhs', 'rhs', 'operator');
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
        return [
            ['Hello @contact.name', [
                'Hello ',
                [
                    'type' => 'MEMBER',
                    'key' => 'contact.name'
                ]
            ]],
            ['Hello @contact', [
                'Hello ',
                [
                    'type' => 'MEMBER',
                    'key' => 'contact',
                ]
            ]],
            ['@person.lastname you are special', [
                [
                    'type' => 'MEMBER',
                    'key' => 'person.lastname',
                ],
                ' you are special'
            ]],
            ['Hello @(contact.lastname)', [
                'Hello ', [
                    'type' => 'MEMBER',
                    'key' => 'contact.lastname'
                ]
            ]],
            ['Hello @(contact)', [
                'Hello ', [
                    'type' => 'MEMBER',
                    'key' => 'contact',
                ]
            ]],
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
            ['The date is @(NOW())', [
                'The date is ',
                $this->methodNode('NOW', [])
            ]],
            ['The date is @NOW()', [
                'The date is ',
                $this->methodNode('NOW', [])
            ]],
            ['@(DATE(2012, 12, 25)) was a holiday.', [
                $this->methodNode('DATE', ['2012', '12', '25']),
                ' was a holiday.',
            ]],
            ['@DATE(2012, 12, 25) was a holiday.', [
                $this->methodNode('DATE', ['2012', '12', '25']),
                ' was a holiday.',
            ]],
            ['This @(upper("hello")) is a function.', [
                'This ',
                $this->methodNode('upper', ['hello']),
                ' is a function.'
            ]],
            ['This @upper("hello") is a function.', [
                'This ',
                $this->methodNode('upper', ['hello']),
                ' is a function.'
            ]],
        ];
    }

    public function multipleExpressionProvider()
    {
        // last element is number of expected AST nodes
        return [
            ['Hello @contact.name today is @(NOW())', 4],
            ['We can be reached at @organization.phone except on @(DATE(2012, 12, 24)) or @(DATE(2012, 12, 25))', 6]
        ];
    }

    public function nestedFunctionProvider()
    {
        // 2nd param is the index of the method in the ast
        // 3rd param is expected the number of expressions as arguments
        return [
            ['Your name is @(UPPER(contact.name))', 1, 1],
            ['Your full name is @(UPPER(contact.firstname, contact.lastname))', 1, 2],
            ['The sum is @(SUM(contact.age, MIN(12, contact.age)))', 1, 2],
            ['The sum is @(SUM(2 + 2, contact.age, 42 / 6, 8 * 2))', 1, 4]
        ];
    }

    public function simpleMathProvider()
    {
        return [
            ['Some math is @(1 + 2)', [
                'Some math is ',
                $this->mathNode(1, 2, '+')
            ]],
            ['@(4 - 3) no spaces', [
                $this->mathNode(4, 3, '-'),
                ' no spaces'
            ]],
            ['@(6 / 2) is three', [
                $this->mathNode(6, 2, '/'),
                ' is three',
            ]],
            ['@(7 * 77) is hard', [
                $this->mathNode(7, 77, '*'),
                ' is hard'
            ]],
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
        return [
            ['Some logic is @(1 < 2)', [
                'Some logic is ',
                $this->logicNode(1, 2, '<')
            ]],
            ['@(0 = 0) no spaces', [
                $this->logicNode(0, 0, '='),
                ' no spaces'
            ]],
            ['@(6 >= 2) is true', [
                $this->logicNode(6, 2, '>='),
                ' is true'
            ]],
            ['@(7 <= 77) is false', [
                $this->logicNode(7, 77, '<='),
                ' is false'
            ]],
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

    public function regressionProvider() {
        return [
            // test evaluation of logic with complex lhs or rhs
            ['flow.case2 = flow.case1 + flow.case3 + flow.case4 + flow.case5 + flow.case6'],
            ['flow.case1 + flow.case3 + flow.case4 + flow.case5 + flow.case6 = flow.case2 '],
        ];
    }
}
