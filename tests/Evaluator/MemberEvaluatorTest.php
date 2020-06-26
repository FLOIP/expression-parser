<?php

namespace Viamo\Floip\Tests\Evaluator;

use PHPUnit\Framework\TestCase;
use Viamo\Floip\Evaluator\MemberNodeEvaluator;
use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\Node;
use Viamo\Floip\Tests\Evaluator\Mocks\MockArrayContext;

class MemberNodeEvaluatorTest extends TestCase
{
    /** @var MemberNodeEvaluator */
    private $evaluator;

    public function setUp()
    {
        $this->evaluator = new MemberNodeEvaluator;
    }

    /**
     * @dataProvider absentKeyProvider
     */
    public function testEvaluatesAbsentKey(Node $node, array $context, $expected) {
        $evaluated = $this->evaluator->evaluate($node, $context);
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider arrayReturnProvider
     */
    public function testArrayReturn(Node $node, array $context, $expected) {
        $evaluated = $this->evaluator->evaluate($node, $context);
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider keyAndValueProvider
     */
    public function testEvaluatesKeyAndValue(Node $node, array $context, $expected)
    {
        $evaluated = $this->evaluator->evaluate($node, $context);
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider keyNoValueProvider
     */
    public function testEvaluatesKeyNoValue(Node $node, array $context, $expected)
    {
        $evaluated = $this->evaluator->evaluate($node, $context);
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider keyDefaultValueProvider
     */
    public function testEvaluatesKeyWithDefaultValue(Node $node, array $context, $expected)
    {
        $evaluated = $this->evaluator->evaluate($node, $context);
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider nestedContextProvider
     */
    public function testNestedContext(Node $node, array $context, $expected)
    {
        $evaluated = $this->evaluator->evaluate($node, $context);
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider numericKeyProvider
     */
    public function testNumericKeys(Node $node, array $context, $expected) {
        $evaluated = $this->evaluator->evaluate($node, $context);
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider absentKeyProvider
     */
    public function testEvaluatesAbsentKeyObjectContext(Node $node, array $context, $expected) {
        $evaluated = $this->evaluator->evaluate($node, new MockArrayContext($context));
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider arrayReturnProvider
     */
    public function testArrayReturnObjectContext(Node $node, array $context, $expected) {
        $evaluated = $this->evaluator->evaluate($node, new MockArrayContext($context));
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider keyAndValueProvider
     */
    public function testEvaluatesKeyAndValueObjectContext(Node $node, array $context, $expected)
    {
        $evaluated = $this->evaluator->evaluate($node, new MockArrayContext($context));
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider keyNoValueProvider
     */
    public function testEvaluatesKeyNoValueObjectContext(Node $node, array $context, $expected)
    {
        $evaluated = $this->evaluator->evaluate($node, new MockArrayContext($context));
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider keyDefaultValueProvider
     */
    public function testEvaluatesKeyWithDefaultValueObjectContext(Node $node, array $context, $expected)
    {
        $evaluated = $this->evaluator->evaluate($node, new MockArrayContext($context));
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider nestedContextProvider
     */
    public function testNestedContextObjectContext(Node $node, array $context, $expected)
    {
        $evaluated = $this->evaluator->evaluate($node, new MockArrayContext($context));
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider numericKeyProvider
     */
    public function testNumericKeysObjectContext(Node $node, array $context, $expected) {
        $evaluated = $this->evaluator->evaluate($node, new MockArrayContext($context));
        $this->assertEquals($expected, $evaluated);
    }

    private function makeNode($key, $location = []) {
        return new Node([
            'type' => ParsesFloip::MEMBER_TYPE,
            'key' => $key,
            'location' => $location
        ]);
    }

    public function absentKeyProvider()
    {
        return [
            [
                $this->makeNode('contact.name'),
                [
                    // empty context
                ],
                '@contact.name'
            ],
        ];
    }

    public function arrayReturnProvider()
    {
        return [
            [
                $this->makeNode('flow.multipleChoice.value'),
                [
                    'flow' => [
                        'multipleChoice' => [
                            'value' => ['one', 'two', 'three']
                        ]
                    ]
                ],
                ['one', 'two', 'three']
            ],
        ];
    }

    public function keyAndValueProvider()
    {
        return [
            [
                $this->makeNode('contact.name'),
                [
                    'contact' => [
                        'name' => 'Kyle',
                    ]
                ],
                'Kyle'
            ],

            [
                $this->makeNode('contact.name'),
                [
                    'contact' => [
                        'name' => 'Kyle',
                    ]
                ],
                'Kyle'
            ],

            [
                $this->makeNode('contact.name'),
                [
                    'contact' => [
                        'name' => 'Kyle',
                    ]
                ],
                'Kyle'
            ],

            [
                $this->makeNode('contact.name'),
                [
                    'contact' => [
                        'name' => 'Kyle',
                    ]
                ],
                'Kyle'
            ],
            
        ];
    }

    public function keyNoValueProvider()
    {
        return [
            [
                $this->makeNode('contact'),
                [
                    'contact' => [
                        'name' => 'Kyle',
                        'foo' => 'bar'
                    ]
                ],
                '{"name":"Kyle","foo":"bar"}'
            ],
        ];
    }

    public function keyDefaultValueProvider()
    {
        return [
            [
                $this->makeNode('contact'),
                [
                    'contact' => [
                        'name' => 'Kyle',
                        'foo' => 'bar',
                        '__value__' => 'Some Guy',
                    ]
                ],
                'Some Guy'
            ],
        ];
    }

    public function nestedContextProvider()
    {
        return [
            'nested '=> [
                $this->makeNode('contact.lang.default'),
                [
                    'contact' => [
                        'name' => 'Kyle',
                        'lang' => [
                            'default' => 'en',
                            'available' => [
                                'fr',
                            ],
                        ],
                    ]
                ],
                'en'
            ],
            'deep nested' => [
                $this->makeNode('contact.address.business.city'),
                [
                    'contact' => [
                        'name' => 'Kyle',
                        'address' => [
                            'business' => [
                                'city' => 'Winnipeg'
                            ]
                        ]
                    ]
                ],
                'Winnipeg'
            ],
            'nested key not found no __value__' => [
                $this->makeNode('contact.address.business'),
                [
                    'contact' => [
                        'name' => 'Kyle',
                        'address' => [
                            'business' => [
                                'foo' => 'bar'
                            ]
                        ]
                    ]
                ],
                \json_encode((object)['foo' => 'bar'])
            ],
            'nested key not found with __value__' => [
                $this->makeNode('contact.address.business'),
                [
                    'contact' => [
                        'name' => 'Kyle',
                        'address' => [
                            'business' => [
                                'foo' => 'bar',
                                '__value__' => '42'
                            ]
                        ]
                    ]
                ],
                '42'
            ],
            'key not found' => [
                $this->makeNode('contact.address.business.city'),
                [
                    'contact' => [
                        'name' => 'Kyle',
                        'address' => [
                            'business' => [
                                'foo' => 'bar',
                                '__value__' => '42'
                            ]
                        ]
                    ]
                ],
                'contact.address.business.city'
            ],
        ];
    }

    public function numericKeyProvider() {
        return [
            [
                $this->makeNode('flow.1570165060485_42.value'),
                [
                    'flow' => [
                        '1570165060485_42' => [
                            'value' => 'Some Value',
                            '__value__' => 'Some Default'
                        ]
                    ]
                ],
                'Some Value'
            ],
            [
                $this->makeNode('flow.1570165060485_42'),
                [
                    'flow' => [
                        '1570165060485_42' => [
                            'value' => 'Some Value',
                            '__value__' => 'Some Default'
                        ]
                    ]
                ],
                'Some Default'
            ],
        ];
    }
}
