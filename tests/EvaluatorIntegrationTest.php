<?php

namespace Viamo\Floip\Tests;

use PHPUnit\Framework\TestCase;
use Viamo\Floip\Parser;
use Viamo\Floip\Evaluator;
use Viamo\Floip\Evaluator\MethodEvaluator;
use Viamo\Floip\Evaluator\MemberEvaluator;
use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Contract\EvaluatesExpression;
use Viamo\Floip\Evaluator\MethodEvaluator\Logical;
use Viamo\Floip\Evaluator\LogicEvaluator;
use Viamo\Floip\Evaluator\MethodEvaluator\DateTime;
use Viamo\Floip\Evaluator\MethodEvaluator\Math;
use Carbon\Carbon;
use Viamo\Floip\Evaluator\MethodEvaluator\Text;

class EvaluatorIntegrationTest extends TestCase
{
    /** @var Evaluator */
    protected $evaluator;
    /** @var Parser */
    protected $parser;
    /** @var EvaluatesExpression */
    protected $methodEvaluator;
    /** @var EvaluatesExpression */
    protected $memberEvaluator;
    /** @var EvaluatesExpression */
    protected $logicEvaluator;

    public function setUp()
    {
        $this->parser = new Parser;
        $this->methodEvaluator = new MethodEvaluator;
        $this->memberEvaluator = new MemberEvaluator;
        $this->logicEvaluator = new LogicEvaluator;


        $this->evaluator = new Evaluator($this->parser);
        $this->evaluator->addNodeEvaluator($this->methodEvaluator, ParsesFloip::METHOD_TYPE);
        $this->evaluator->addNodeEvaluator($this->memberEvaluator, ParsesFloip::MEMBER_TYPE);
        $this->evaluator->addNodeEvaluator($this->logicEvaluator, ParsesFloip::LOGIC_TYPE);
    }

    public function testEvaluatesMemberAccess()
    {
        $expression = 'Hello @contact.name';
        $expected = 'Hello Kyle';
        $context = [
            'contact' => [
                'name' => 'Kyle',
            ]
        ];
        
        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    public function testEvaluatesMethod()
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);
        $expression = 'Today is @(NOW())';
        $expected = "Today is $now";
        $context = [];
        $this->methodEvaluator->addHandler(new DateTime);
        
        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);        
    }

    public function testEvaluatesMethodWithArgs()
    {
        $expression = 'Today is @(DATE(2012,12,12))';
        $expected = "Today is " . Carbon::createFromDate(2012, 12, 12);
        $context = [];
        $this->methodEvaluator->addHandler(new DateTime);
        
        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);   
    }

    public function testEvaluatesNestedMethod()
    {
        $now = Carbon::now();
        $expression = 'Today is @(DATE(YEAR(NOW()), MONTH(NOW()), DAY(NOW())))';
        $expected = "Today is " . Carbon::createFromDate($now->year, $now->month, $now->day);
        $context = [];
        $this->methodEvaluator->addHandler(new DateTime);

        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);   
    }

    public function testEvaluatesMultipleMethods()
    {
        $now = Carbon::now();
        $nowString = Carbon::createFromDate($now->year, $now->month, $now->day);
        $expression = 'Today is @(DATE(YEAR(NOW()), MONTH(NOW()), DAY(NOW()))), or just @(NOW())';
        $expected = "Today is $nowString, or just $nowString";
        $context = [];

        $this->methodEvaluator->addHandler(new DateTime);

        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);   
    }

    public function testEvaluatesMethodWithMemberArg()
    {
        $now = Carbon::now();
        $nowString = Carbon::createFromDate($now->year, $now->month, $now->day);
        $expression = 'Today is @(DATE(contact.year, contact.month, contact.day))';
        $expected = "Today is $nowString";
        $context = [
            'contact' => [
                'year' => $now->year,
                'month' => $now->month,
                'day' => $now->day
            ]
        ];

        $this->methodEvaluator->addHandler(new DateTime);

        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);  
    }

    /**
     * @dataProvider mathProvider
     */
    public function testEvaluatesMath($expression, $expected)
    {
        $this->methodEvaluator->addHandler(new Math);

        $result = $this->evaluator->evaluate($expression, []);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider logicProvider
     */
    public function testEvaluatesLogic($expression, array $context, $expected)
    {
        $this->methodEvaluator->addHandler(new Logical);

        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }
    
    /**
     * @dataProvider textMethodsProvider
     */
    public function testEvaluatesTextMethods($expression, array $context, $expected)
    {
        $this->methodEvaluator->addHandler(new Text);

        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    public function mathProvider()
    {
        return [
            ['@(abs(-6))', '6'],
            ['@(max(6, 19, 7))', '19'],
            ['@(min(7, 6, 19))', '6'],
            ['@(power(2,2))', '4'],
            ['@(sum(2,3,4))', '9'],
        ];
    }

    public function logicProvider()
    {
        return [
            ['@(and(contact.gender = "f", contact.age >= 10))',[
                'contact' => [
                    'gender' => 'f',
                    'age' => '9',
                ]], 'TRUE'
            ],
        ];
    }

    public function textMethodsProvider()
    {
        return [
            'char' => ['As easy as @CHAR(65), @CHAR(66), @CHAR(67)', [], 'As easy as A, B, C'],
            'clean' => ['You entered @CLEAN(step.value)', [
                'step' => [
                    'value' => "\n\t\rABC"
                ]
            ], 'You entered ABC'],
            'code' => ['The numeric code of A is @CODE("A")', [], 'The numeric code of A is 65'],
            'concat' => ['Your name is @CONCATENATE(contact.first_name, " ", contact.last_name)',[
                'contact' => [
                    'first_name' => 'Big',
                    'last_name' => 'Papa'
                ]]
                , 'Your name is Big Papa' 
            ],
            'fixed' => ['You have @FIXED(contact.balance, 2) in your account', [
                'contact' => ['balance' => '4.209922']
            ], 'You have 4.20 in your account'],
            'left' => ['You entered PIN @LEFT(step.value, 4)', [
                'step' => ['value' => '1234567']
            ], 'You entered PIN 1234'],
            'len' => ['You entered @LEN(step.value) characters', [
                'step' => ['value' => '7654321']
            ], 'You entered 7 characters'],
            'lower' => ['Welcome @LOWER(contact)', ['contact' => ['__value__' => 'JOHN']], 'Welcome john'],
            'proper' => ['Your name is @PROPER(contact)', [
                'contact' => ['__value__' => 'jAcOb JoNeS']
            ], 'Your name is Jacob Jones'],
            'rept' => ['Stars! @(REPT("*", 10))', [], 'Stars! **********'],
            'right' => ['Your input ended with ...@(RIGHT(step.value, 3))', [
                'step' => ['value' => 'Hello World']
            ], 'Your input ended with ...rld'],
            'substitute' => ['@SUBSTITUTE(step.value, "can\'t", "can")', [
                'step' => ['value' => 'I can\'t do it']
            ], 'I can do it'],
            'upper' => ['WELCOME @(UPPER(contact))!!', 
                ['contact' => ['__value__' => 'home']],
                'WELCOME HOME!!']
        ];
    }
}
