<?php

namespace Floip\Tests;

use PHPUnit\Framework\TestCase;
use Floip\Parser;
use Floip\Evaluator;
use Floip\Evaluator\MethodEvaluator;
use Floip\Evaluator\MemberEvaluator;
use Floip\Contract\ParsesFloip;
use Floip\Contract\EvaluatesExpression;
use Floip\Evaluator\MethodEvaluator\Logical;
use Floip\Evaluator\LogicEvaluator;
use Floip\Evaluator\MethodEvaluator\DateTime;
use Floip\Evaluator\MethodEvaluator\Math;
use Carbon\Carbon;

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
}
