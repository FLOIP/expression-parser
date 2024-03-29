<?php

namespace Viamo\Floip\Tests;

use ArrayObject;
use Carbon\Carbon;
use Viamo\Floip\Parser;
use Viamo\Floip\Evaluator;
use Viamo\Floip\Tests\TestCase;
use Viamo\Floip\Evaluator\MathNodeEvaluator;
use Viamo\Floip\Contract\EvaluatesExpression;
use Viamo\Floip\Evaluator\BoolNodeEvaluator;
use Viamo\Floip\Evaluator\LogicNodeEvaluator;
use Viamo\Floip\Evaluator\EscapeNodeEvaluator;
use Viamo\Floip\Evaluator\MemberNodeEvaluator;
use Viamo\Floip\Evaluator\MethodNodeEvaluator;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Math;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Text;
use Viamo\Floip\Evaluator\ConcatenationNodeEvaluator;
use Viamo\Floip\Evaluator\Exception\EvaluatorException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\ArrayHandler;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Logical;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\DateTime;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Excellent;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\MatchTest;
use Viamo\Floip\Evaluator\NullNodeEvaluator;

class EvaluatorIntegrationTest extends TestCase
{
    /** @var Evaluator */
    protected Evaluator $evaluator;
    /** @var Parser */
    protected Parser $parser;
    /** @var MethodNodeEvaluator */
    protected MethodNodeEvaluator $MethodNodeEvaluator;
    /** @var EvaluatesExpression */
    protected EvaluatesExpression|MemberNodeEvaluator $MemberNodeEvaluator;
    /** @var EvaluatesExpression */
    protected EvaluatesExpression|LogicNodeEvaluator $LogicNodeEvaluator;
    /** @var MathNodeEvaluator */
    protected MathNodeEvaluator $mathNodeEvaluator;
    /** @var EvaluatesExpression */
    protected EvaluatesExpression|EscapeNodeEvaluator $escapeNodeEvaluator;
    /** @var EvaluatesExpression */
    protected ConcatenationNodeEvaluator|EvaluatesExpression $concatenationNodeEvaluator;
    /** @var EvaluatesExpression */
    protected EvaluatesExpression|NullNodeEvaluator $nullNodeHandler;
    /** @var EvaluatesExpression */
    protected BoolNodeEvaluator|EvaluatesExpression $boolNodeEvaluator;

    public function setUp(): void
    {
        $this->parser = new Parser;
        $this->MethodNodeEvaluator = new MethodNodeEvaluator;
        $this->MemberNodeEvaluator = new MemberNodeEvaluator;
        $this->LogicNodeEvaluator = new LogicNodeEvaluator;
        $this->mathNodeEvaluator = new MathNodeEvaluator;
        $this->escapeNodeEvaluator = new EscapeNodeEvaluator;
        $this->concatenationNodeEvaluator = new ConcatenationNodeEvaluator;
        $this->nullNodeHandler = new NullNodeEvaluator;
        $this->boolNodeEvaluator = new BoolNodeEvaluator;

        $this->evaluator = new Evaluator($this->parser);
        $this->evaluator->addNodeEvaluator($this->MethodNodeEvaluator);
        $this->evaluator->addNodeEvaluator($this->MemberNodeEvaluator);
        $this->evaluator->addNodeEvaluator($this->LogicNodeEvaluator);
        $this->evaluator->addNodeEvaluator($this->mathNodeEvaluator);
        $this->evaluator->addNodeEvaluator($this->escapeNodeEvaluator);
        $this->evaluator->addNodeEvaluator($this->concatenationNodeEvaluator);
        $this->evaluator->addNodeEvaluator($this->nullNodeHandler);
        $this->evaluator->addNodeEvaluator($this->boolNodeEvaluator);

        parent::setUp();
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

    public function testEvaluatesDateStringMethod()
    {
        $now = Carbon::now();
        $expression = 'Today is @(DATE(YEAR(date.now), MONTH(date.now), DAY(date.now)))';
        $expected = "Today is " . Carbon::parse('2020-01-02T20:20:20Z')->startOfDay();
        $context = [
            'date' => [
                'now' => '2020-01-02T20:20:20Z'
            ]
        ];
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
        $this->MethodNodeEvaluator->addHandler(new ArrayHandler);
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


    /**
     * @dataProvider dateStringProvider
     */
    public function testDateStrings($expression, array $context, $expected) {
        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider arrayFunctionProvider
     */
    public function testArrayFunctions($expression, array $context, $expected) {
        $this->MethodNodeEvaluator->addHandler(new ArrayHandler);
        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider boolLogicProvider
     */
    public function testBoolLogic($expression, array $context, $expected) {
        $result = $this->evaluator->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    public function testWillAcceptNestedArrayAccessContext() {
        $context = new ArrayObject([
            'foo' => new ArrayObject([
                'bar' => '42',
                '__value__' => '42'
            ]),
            'foobar' => new ArrayObject([
                'barbar' => new ArrayObject([
                    'baz' => '420',
                    '__value__' => '420'
                ])
            ])
        ]);

        $expression = '@foo.bar @foo @foobar.barbar @foobar.barbar.baz';
        $expected   = '42 42 420 420';

        $this->assertEquals($expected, $this->evaluator->evaluate($expression, $context));
    }

    public function testWillThrowMeaningfulErrors() {
        $context = ['bar' => ['a', 'b']];
        $expression = "@contains('foo', bar)";

        $this->expectException(EvaluatorException::class);
        $this->expectExceptionMessageMatches("/@contains\('foo', bar\)/");
        $this->evaluator->evaluate($expression, $context);
    }

    public function testCastsMatchTestResultsForLogicMethods(): void {
        $context = [];
        $this->MethodNodeEvaluator->addHandler(new MatchTest);
        $this->MethodNodeEvaluator->addHandler(new Text);
        $this->MethodNodeEvaluator->addHandler(new Excellent);
        $this->MethodNodeEvaluator->addHandler(new Logical);
        $expression = "@OR(len('Soupe') <2, len('Soupe') >34, word_count('Soupe') >1, has_number('Soupe'))";
        $result = $this->evaluator->evaluate($expression, $context);
        $this->assertEquals('FALSE', $result);
    }

    public function boolLogicProvider(): array {
        return [
            0 => [
                '@(block.value = null)',
                [],
                'FALSE'
            ],
            1 => [
                '@(block.value = null)',
                ['block' => ['value' => 'foo']],
                'FALSE'
            ],
            2 => [
                '@(block.value = null)',
                ['block' => ['value' => '']],
                'TRUE'
            ],
        ];
    }

    public function arrayFunctionProvider(): array {
        return [
            'array to string' => [
                '@(array("foo", "bar", "baz"))',
                [],
                'foo, bar, baz'
            ],
            'array to string, empty' => [
                '@array()',
                [],
                ''
            ],
            'in array, positive 1' => [
                '@(in("baz", array("foo", "bar", "baz")))',
                [],
                'TRUE'
            ],
            'in array, positive 2' => [
                '@(in("bar", array("foo", "bar", "baz")))',
                [],
                'TRUE'
            ],
            'in array, negative' => [
                '@(in("fuz", array("foo", "bar", "baz")))',
                [],
                'FALSE'
            ],
            'in array, empty' => [
                '@(in("foo", array()))',
                [],
                'FALSE',
            ],
            'count' => [
                '@(count(array("foo", "bar", "baz")))',
                [],
                '3'
            ],
            'count' => [
                '@(count(array()))',
                [],
                '0'
            ],
            'in with object true' => [
                '@in("foo", groups)',
                [
                    'groups' => [
                        [
                            'value' => 'foo',
                            '__value__' => 'foo'
                        ]
                    ]
                ],
                'TRUE'
            ],
            'in with object false' => [
                '@in("bar", groups)',
                [
                    'groups' => [
                        [
                            'value' => 'foo',
                            '__value__' => 'foo'
                        ]
                    ]
                ],
                'FALSE'
            ]
        ];
    }

    public function nullExpressionProvider(): array {
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

    public function concatenationExpressionProvider(): array {
        return [
            'simple case' => ['@("Hello " & "World")', [], 'Hello World'],
            'multiple' => ['@("One" & " " & "Two")', [], 'One Two'],
            'with member access' => ['@(context.firstname & " " & context.lastname)', [
                'context' => ['firstname' => 'John', 'lastname' => 'Smith']
            ], 'John Smith'],
            'with math' => ['@("Two plus" & " " & "two: " & 2 + 2)', [], 'Two plus two: 4']
        ];
    }

    public function escapedExpressionProvider(): array {
        return [
            ['Follow us @@twitterHandle', [], 'Follow us @twitterHandle'],
            ['Email us @@ contact@@example.com', [], 'Email us @ contact@example.com'],
        ];
    }

    public function mathExpressionProvider(): array {
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

    public function mathFnProvider(): array {
        return [
            ['@(abs(-6))', '6'],
            ['@(max(6, 19, 7))', '19'],
            ['@(min(7, 6, 19))', '6'],
            ['@(power(2,2))', '4'],
            ['@(sum(2,3,4))', '9'],
        ];
    }

    public function logicProvider(): array {
        return [
            ['@(and(contact.gender = "f", contact.age >= 10))', [
                'contact' => [
                    'gender' => 'f',
                    'age' => '9',
                ]
            ], 'FALSE'],
            ['@(and(1 = 1, 3 = 3))', [], 'TRUE'],
            ['@(TRUE <> FALSE)', [], 'TRUE']
        ];
    }

    public function textMethodsProvider(): array {
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
            'contains' => [ '@(CONTAINS(contact.needle, contact.haystack))', [
                'contact' => [
                    'needle' => 'Raptors',
                    'haystack' => 'Toronto Raptors'
                ]
            ], 'TRUE'],
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

    public function flowProvider(): array {
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
            ],
            'VMO-5857' => [
                '@(SUM(flow.expressionq2, flow.expressionq3, flow.expressionq4, flow.expressionq5, flow.expressionq6, flow.expressionq7, flow.expressionq8))',
                [
                    'flow' => [
                        'expressionq2' => [ '__value__' => '1'],
                        'expressionq3' => [ '__value__' => '1'],
                        'expressionq6' => [ '__value__' => '1'],
                    ]
                ],
                '3'
            ],
            'VMO-5857-2' => [
                '@SUM(flow.expressionq2, flow.expressionq3, flow.expressionq4, flow.expressionq5, flow.expressionq6, flow.expressionq7, flow.expressionq8)',
                [
                    'flow' => [
                        'expressionq2' => [ '__value__' => 1],
                        'expressionq3' => [ '__value__' => 1],
                        'expressionq6' => [ '__value__' => 1],
                    ]
                ],
                '3'
            ],
            'VMO-5857-2-2' => [
                '@SUM(flow.expressionq2, flow.expressionq3, flow.expressionq4, flow.expressionq5, flow.expressionq6, flow.expressionq7, flow.expressionq8)',
                [
                    'flow' => [
                        'expressionq2' => [ '__value__' => 30],
                        'expressionq3' => [ '__value__' => 30],
                        'expressionq6' => [ '__value__' => 30],
                    ]
                ],
                '90'
            ],
            'VMO-5857 2' => [
                "@(FIXED((flow.endlinesum/7)*100, 2))",
                [
                    'flow' => [
                        'endlinesum' => '3'
                    ]
                ],
                '42.85'
            ],
            'VMO-5624' => [
                "@(LEN(block.value) > 0)",
                [
                    "flow" => [
                        'block' => '4'
                    ]
                    ],
                'TRUE'
                ],
            'Serigne' => [
                "@(if(flow.q2=16, 3, 0))",
                [
                    'flow' => [
                        'q2' => [
                            '__value__' => '16',
                            'value' => '16'
                        ]
                    ]
                ],
                '3'
            ],
            'Hannah' => [
                "@(AND(IN(groups.demo_day_clinic, contact.groups), IN(groups.seller, contact.groups)))",
                [
                    "contact" => [
                        'groups' => [
                            'demo_day_clinic',
                            'seller',
                        ]
                    ],
                    'groups' => [
                        'demo_day_clinic' => [
                            '__value__' => 'demo_day_clinic',
                        ],
                        'seller' => [
                            '__value__' => 'seller',
                        ],
                    ]
                ],
                'TRUE'
            ],
            'logic on stringy variable' => [
                "@AND(contact.completed_module_1_lesson_2 = TRUE)",
                [],
                'FALSE'
            ],
            'logic on stringy "true"' => [
                "@AND('TRUE' = TRUE)",
                [],
                'TRUE'
            ],
            'logic on numeric variable 0' => [
                "@AND(0 = TRUE)",
                [],
                'FALSE'
            ],
            'logic on numeric variable 1' => [
                "@AND(1 = TRUE)",
                [],
                'FALSE'
            ],
        ];
    }

    public function isXXXProvider(): array {
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
            ],
            'isxxx all together now but snake case' => [
                '@(AND(is_bool("TRUE"), is_string("foo"), is_number("5")))',
                [],
                'TRUE'
            ]
        ];
    }

    public function emailsProvider(): array {
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

    public function dateStringProvider(): array {
        $now = Carbon::parse("2020-02-07 00:00:00");
        Carbon::setTestNow($now);
        return [
            'vmo-2586' => [
                "7 days from today is @(date.today + '7 days')",
                [
                    'date' => [
                        '__value__' => $now->toDateTimeString(),
                        'now' => $now->toDateString(),
                        'today' => "2020-02-07",
                        'yesterday' => $now->yesterday()->toDateString(),
                        'tomorrow' => $now->tomorrow()->toDateString(),
                    ]
                ],
                "7 days from today is 2020-02-14 00:00:00",
            ]
        ];
    }


    public function testInGroupsNestedMemberObject() {
        $e = "@(IN(groups.group, contact.groups))";
        $c = [
            'groups' => [
                'group' => [
                    '__value__' => 'group0'
                ],
                'group1'
            ],
            'contact' => [
                'groups' => [
                    'group0' => [
                        '__value__' => 'group0'
                    ],
                    ['group1' => [
                        '__value__' => 'group1'
                    ]]
                ]
            ]
                ];
        $this->MethodNodeEvaluator->addHandler(new ArrayHandler);
        $this->assertEquals('TRUE', $this->evaluator->evaluate($e, $c));
        $this->assertEquals('2', $this->evaluator->evaluate('@(COUNT(contact.groups))', $c));
    }

    public function testBoolKeywordEvaluation() {
        $e = "Hello @(true) it's @(false) and @(true = true) but not @(true = false)";
        $c = [];
        $this->assertEquals('Hello TRUE it\'s FALSE and TRUE but not FALSE', $this->evaluator->evaluate($e, $c));
    }

    public function testIfWithNullValue() {
        $e = "@(IF(ISNUMBER(flow.1620421744601_32.value), flow.1620421744601_32.value, NULL))";
        $c = [
            'flow' => [
                '1620421744601_32' => [
                    'value' => 'String'
                ]
            ]
        ];
        $this->MethodNodeEvaluator->addHandler(new Excellent);
        $this->MethodNodeEvaluator->addHandler(new Logical);
        $this->assertEquals('NULL', $this->evaluator->evaluate($e, $c));
    }

    public function testIfWithEmptyStringValue() {
        $e = "@(IF(ISNUMBER(flow.1620421744601_32.value), flow.1620421744601_32.value, ''))";
        $c = [
            'flow' => [
                '1620421744601_32' => [
                    'value' => 'String'
                ]
            ]
        ];
        $this->MethodNodeEvaluator->addHandler(new Excellent);
        $this->MethodNodeEvaluator->addHandler(new Logical);
        $this->assertEquals('', $this->evaluator->evaluate($e, $c));
    }

    public function testRouterTestMethod() {
        $e = "@(has_any_word('The Quick Brown Fox', 'fox quick'))";
        $c = [];
        $this->MethodNodeEvaluator->addHandler(new MatchTest);
        $this->assertEquals('TRUE', $this->evaluator->evaluate($e, $c));
    }

    public function testRouterTestMethodChaining() {
        $e = "@(has_any_word('The Quick Brown Fox', 'fox quick').match)";
        $c = [];
        $this->MethodNodeEvaluator->addHandler(new MatchTest);
        $this->assertEquals('Quick Fox', $this->evaluator->evaluate($e, $c));
    }
}
