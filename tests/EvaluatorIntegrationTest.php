<?php

namespace Viamo\Floip\Tests;

use Carbon\Carbon;
use Viamo\Floip\Parser;
use Viamo\Floip\Evaluator;
use PHPUnit\Framework\TestCase;
use Viamo\Floip\Evaluator\MathNodeEvaluator;
use Viamo\Floip\Contract\EvaluatesExpression;
use Viamo\Floip\Evaluator\LogicNodeEvaluator;
use Viamo\Floip\Evaluator\EscapeNodeEvaluator;
use Viamo\Floip\Evaluator\MemberNodeEvaluator;
use Viamo\Floip\Evaluator\MethodNodeEvaluator;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Math;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Text;
use Viamo\Floip\Evaluator\ConcatenationNodeEvaluator;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Logical;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\DateTime;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Excellent;
use Viamo\Floip\Evaluator\NullNodeEvaluator;

class EvaluatorIntegrationTest extends TestCase
{
    /** @var Evaluator */
    protected $evaluator;
    /** @var Parser */
    protected $parser;
    /** @var MethodNodeEvaluator */
    protected $MethodNodeEvaluator;
    /** @var EvaluatesExpression */
    protected $MemberNodeEvaluator;
    /** @var EvaluatesExpression */
    protected $LogicNodeEvaluator;
    /** @var MathNodeEvaluator */
    protected $mathNodeEvaluator;
    /** @var EvaluatesExpression */
    protected $escapeNodeEvaluator;
    /** @var EvaluatesExpression */
    protected $concatenationNodeEvaluator;
    /** @var EvaluatesExpression */
    protected $nullNodeHandler;

    public function setUp()
    {
        $this->parser = new Parser;
        $this->MethodNodeEvaluator = new MethodNodeEvaluator;
        $this->MemberNodeEvaluator = new MemberNodeEvaluator;
        $this->LogicNodeEvaluator = new LogicNodeEvaluator;
        $this->mathNodeEvaluator = new MathNodeEvaluator;
        $this->escapeNodeEvaluator = new EscapeNodeEvaluator;
        $this->concatenationNodeEvaluator = new ConcatenationNodeEvaluator;
        $this->nullNodeHandler = new NullNodeEvaluator;

        $this->evaluator = new Evaluator($this->parser);
        $this->evaluator->addNodeEvaluator($this->MethodNodeEvaluator);
        $this->evaluator->addNodeEvaluator($this->MemberNodeEvaluator);
        $this->evaluator->addNodeEvaluator($this->LogicNodeEvaluator);
        $this->evaluator->addNodeEvaluator($this->mathNodeEvaluator);
        $this->evaluator->addNodeEvaluator($this->escapeNodeEvaluator);
        $this->evaluator->addNodeEvaluator($this->concatenationNodeEvaluator);
        $this->evaluator->addNodeEvaluator($this->nullNodeHandler);
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
        $this->MethodNodeEvaluator->addHandler(new DateTime);

        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    public function testEvaluatesMethodWithArgs()
    {
        $expression = 'Today is @(DATE(2012,12,12))';
        $expected = "Today is " . Carbon::createFromDate(2012, 12, 12)->startOfDay();
        $context = [];
        $this->MethodNodeEvaluator->addHandler(new DateTime);

        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    public function testEvaluatesNestedMethod()
    {
        $now = Carbon::now();
        $expression = 'Today is @(DATE(YEAR(NOW()), MONTH(NOW()), DAY(NOW())))';
        $expected = "Today is " . Carbon::createFromDate($now->year, $now->month, $now->day)->startOfDay();
        $context = [];
        $this->MethodNodeEvaluator->addHandler(new DateTime);

        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    public function testEvaluatesMultipleMethods()
    {
        $now = Carbon::now();
        $nowString = Carbon::createFromDate($now->year, $now->month, $now->day)->startOfDay();
        $expression = 'Today is @(DATE(YEAR(NOW()), MONTH(NOW()), DAY(NOW()))), or just @(NOW())';
        $expected = "Today is $nowString, or just $now";
        $context = [];

        $this->MethodNodeEvaluator->addHandler(new DateTime);

        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    public function testEvaluatesMethodWithMemberArg()
    {
        $now = Carbon::now();
        $nowString = Carbon::createFromDate($now->year, $now->month, $now->day)->startOfDay();
        $expression = 'Today is @(DATE(contact.year, contact.month, contact.day))';
        $expected = "Today is $nowString";
        $context = [
            'contact' => [
                'year' => $now->year,
                'month' => $now->month,
                'day' => $now->day
            ]
        ];

        $this->MethodNodeEvaluator->addHandler(new DateTime);

        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider mathFnProvider
     */
    public function testEvaluatesMathFunctions($expression, $expected)
    {
        $this->MethodNodeEvaluator->addHandler(new Math);

        $result = $this->evaluator->evaluate($expression, []);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider logicProvider
     */
    public function testEvaluatesLogic($expression, array $context, $expected)
    {
        $this->MethodNodeEvaluator->addHandler(new Logical);

        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider textMethodsProvider
     */
    public function testEvaluatesTextMethods($expression, array $context, $expected)
    {
        $this->MethodNodeEvaluator->addHandler(new Text);

        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider mathExpressionProvider
     */
    public function testEvaluatesMathExpressions($expression, array $context, $expected)
    {
        $this->MethodNodeEvaluator->addHandler(new Math);
        $this->MethodNodeEvaluator->addHandler(new DateTime);

        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider escapedExpressionProvider
     */
    public function testEscapedExpressions($expression, array $context, $expected)
    {
        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider concatenationExpressionProvider
     */
    public function testConcatenationExpressions($expression, array $context, $expected)
    {
        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider flowProvider
     */
    public function testFlowExpressions($expression, array $context, $expected)
    {
        $this->MethodNodeEvaluator->addHandler(new Logical);
        $this->MethodNodeEvaluator->addHandler(new Text);
        $this->MethodNodeEvaluator->addHandler(new Math);
        $this->MethodNodeEvaluator->addHandler(new DateTime);
        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider  isXXXProvider
     */
    public function testIsXXXFunctions($expression, array $context, $expected)
    {
        $this->MethodNodeEvaluator->addHandler(new Excellent);
        $this->MethodNodeEvaluator->addHandler(new Text);
        $this->MethodNodeEvaluator->addHandler(new Logical);
        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider nullExpressionProvider
     */
    public function testNullExpression($expression, array $context, $expected)
    {
        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider emailsProvider
     */
    public function testEmailsNotClobbered($expression, array $context, $expected) {
        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    public function nullExpressionProvider()
    {
        return [
            [
                '@(contact.name = NULL)',
                ['contact' => ['name' => null]],
                'TRUE'
            ],
            [
                '@(contact.name = NULL)',
                ['contact' => ['name' => 'Kyle']],
                'FALSE'
            ],
            [
                '@(NULL)',
                [],
                'NULL'
            ]
        ];
    }

    public function concatenationExpressionProvider()
    {
        return [
            'simple case' => ['@("Hello " & "World")', [], 'Hello World'],
            'multiple' => ['@("One" & " " & "Two")', [], 'One Two'],
            'with member access' => ['@(context.firstname & " " & context.lastname)', [
                'context' => ['firstname' => 'John', 'lastname' => 'Smith']
            ], 'John Smith'],
            'with math' => ['@("Two plus" & " " & "two: " & 2 + 2)', [], 'Two plus two: 4']
        ];
    }

    public function escapedExpressionProvider()
    {
        return [
            ['Follow us @@twitterHandle', [], 'Follow us @twitterHandle'],
            ['Email us @@ contact@@example.com', [], 'Email us @ contact@example.com'],
        ];
    }

    public function mathExpressionProvider()
    {
        return [
            'simple case' => [
                '2 + 2 is @(2 + 2)', [], '2 + 2 is 4'
            ],
            'nested math' => [
                '@((2 + 4) / 2) is 3', [], '3 is 3'
            ],
            'variable operand' => [
                '@(contact.age + 1) is your age next year', [
                    'contact' => ['age' => '27']
                ], '28 is your age next year',
            ],
            'method operand' => [
                '@(SUM(2,2) + 4) is 8', [], '8 is 8'
            ],
            'bedmas 1' => [
                '@(2 + 4 * 2) is 10', [], '10 is 10'
            ],
            'bedmas 2' => [
                '@((2 + 4) * 2) is 12', [], '12 is 12'
            ],
            'bedmas 3' => [
                '@(1 + (2 - 3) * 4 / 5 ^ 6)', [], '0.999744'
            ],
            'date math with concatenated duration' => [
                '@(DATE(2012,12,12) + flow.appt & " days")', ['flow' => ['appt' => '4']], '2012-12-16 00:00:00'
            ],
        ];
    }

    public function mathFnProvider()
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
            ['@(and(contact.gender = "f", contact.age >= 10))', [
                'contact' => [
                    'gender' => 'f',
                    'age' => '9',
                ]
            ], 'FALSE'],
            ['@(and(1 = 1, 3 = 3))', [], 'TRUE']
        ];
    }

    public function textMethodsProvider()
    {
        return [
            'char' => ['As easy as @(CHAR(65)), @(CHAR(66)), @(CHAR(67))', [], 'As easy as A, B, C'],
            'clean' => ['You entered @(CLEAN(step.value))', [
                'step' => [
                    'value' => "\n\t\rABC"
                ]
            ], 'You entered ABC'],
            'code' => ['The numeric code of A is @(CODE("A"))', [], 'The numeric code of A is 65'],
            'concat' => [
                'Your name is @(CONCATENATE(contact.first_name, " ", contact.last_name))', [
                    'contact' => [
                        'first_name' => 'Big',
                        'last_name' => 'Papa'
                    ]
                ], 'Your name is Big Papa'
            ],
            'fixed' => ['You have @(FIXED(contact.balance, 2)) in your account', [
                'contact' => ['balance' => '4.209922']
            ], 'You have 4.20 in your account'],
            'left' => ['You entered PIN @(LEFT(step.value, 4))', [
                'step' => ['value' => '1234567']
            ], 'You entered PIN 1234'],
            'len' => ['You entered @(LEN(step.value)) characters', [
                'step' => ['value' => '7654321']
            ], 'You entered 7 characters'],
            'lower' => ['Welcome @(LOWER(contact))', ['contact' => ['__value__' => 'JOHN']], 'Welcome john'],
            'proper' => ['Your name is @(PROPER(contact))', [
                'contact' => ['__value__' => 'jAcOb JoNeS']
            ], 'Your name is Jacob Jones'],
            'rept' => ['Stars! @(REPT("*", 10))', [], 'Stars! **********'],
            'right' => ['Your input ended with ...@(RIGHT(step.value, 3))', [
                'step' => ['value' => 'Hello World']
            ], 'Your input ended with ...rld'],
            'substitute' => ['@(SUBSTITUTE(step.value, "can\'t", "can"))', [
                'step' => ['value' => 'I can\'t do it']
            ], 'I can do it'],
            'upper' => [
                'WELCOME @(UPPER(contact))!!',
                ['contact' => ['__value__' => 'home']],
                'WELCOME HOME!!'
            ]
        ];
    }

    public function flowProvider()
    {
        $now = Carbon::parse("2020-02-07 12:00:00");
        Carbon::setTestNow($now);
        return [
            'double quoted' => [
                '@(OR(AND(channel.mode = "ivr", block.value = "7"), AND(channel.mode != "ivr", OR(AND(flow.language = "5", LOWER(block.value)="yup"), AND(flow.language = "5", LOWER(block.value)="1"), AND(flow.language = "5", LOWER(block.value)="yes"), AND(flow.language = "6", LOWER(block.value)="aane"), AND(flow.language = "6", LOWER(block.value)="1"), AND(flow.language = "6", LOWER(block.value)="a")))))',
                [
                    'flow' => [
                        'language' => '5'
                    ],
                    'block' => [
                        'value' => 'YUP'
                    ],
                ],
                'TRUE'
            ],
            'single quoted' => [
                "@(OR(AND(channel.mode = 'ivr', block.value = '7'), AND(channel.mode != 'ivr', OR(AND(flow.language = '5', LOWER(block.value)='yup'), AND(flow.language = '5', LOWER(block.value)='1'), AND(flow.language = '5', LOWER(block.value)='yes'), AND(flow.language = '6', LOWER(block.value)='aane'), AND(flow.language = '6', LOWER(block.value)='1'), AND(flow.language = '6', LOWER(block.value)='a')))))",
                [
                    'flow' => [
                        'language' => '5'
                    ],
                    'block' => [
                        'value' => 'YUP'
                    ],
                ],
                'TRUE'
            ],
            'vmo-1278' => [
                "2 Hours and 30minutes from now is @(date.today + TIME(2, 30, 0)). And your appointment is at @(date.today + TIMEVALUE(\"4:50\")). Today's date is @(TODAY()) and it is day no. @(WEEKDAY(TODAY())) in the week",
                [
                    'date' => [
                        '__value__' => $now->toDateTimeString(),
                        'now' => $now->toDateString(),
                        'today' => $now->toDateString(),
                        'yesterday' => $now->yesterday()->toDateString(),
                        'tomorrow' => $now->tomorrow()->toDateString(),
                    ]
                ],
                "2 Hours and 30minutes from now is 2020-02-07 02:30:00. And your appointment is at 2020-02-07 04:50:00. Today's date is 2020-02-07 and it is day no. 5 in the week",
            ]
        ];
    }

    public function isXXXProvider()
    {
        return [
            'is number true' => [
                '@(isnumber(val.num))',
                ['val' => ['num' => '3']],
                'TRUE'
            ],
            'is number true 2' => [
                '@(isnumber("5"))',
                [],
                'TRUE'
            ],
            'is number false' => [
                '@(isnumber(val.str))',
                ['val' => ['str' => 'nope']],
                'FALSE'
            ],
            'is string true' => [
                '@(isstring("yep"))',
                [],
                'TRUE'
            ],
            'is string false' => [
                '@(isstring(val.num))',
                ['val' => ['num' => '3']],
                'FALSE'
            ],
            'is bool true' => [
                '@(isbool(val.boo))',
                ['val' => ['boo' => true]],
                'TRUE'
            ],
            'is bool false' => [
                '@(isbool(val.boo))',
                ['val' => ['boo' => 'nope']],
                'FALSE'
            ],
            'is bool true string' => [
                '@(isbool("TRUE"))',
                [],
                'TRUE'
            ],
            'is bool false string' => [
                '@(isbool("FALSE"))',
                [],
                'TRUE'
            ],
            'isxxx all together now' => [
                '@(AND(isbool("TRUE"), isstring("foo"), isnumber("5")))',
                [],
                'TRUE'
            ]
        ];
    }

    public function emailsProvider() {
        return [
            [
                'Contact us at foo@example.com',
                [],
                'Contact us at foo@example.com'
            ],
            [
                'Contact us at foo@@example.com',
                [],
                'Contact us at foo@example.com'
            ],
            [
                'Contact us at foo@@contact.com',
                [],
                'Contact us at foo@contact.com'
            ]
        ];
    }
}
