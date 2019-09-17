<?php

namespace Viamo\Floip\Tests\Evaluator;

use PHPUnit\Framework\TestCase;
use Viamo\Floip\Evaluator\MemberNodeEvaluator;
use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\Node;

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
    public function testEvaluatesAbsentKey(array $node, array $context, $expected) {
        $evaluated = $this->evaluator->evaluate(new Node($node), $context);
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider arrayReturnProvider
     */
    public function testArrayReturn(array $node, array $context, $expected) {
        $evaluated = $this->evaluator->evaluate(new Node($node), $context);
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider keyAndValueProvider
     */
    public function testEvaluatesKeyAndValue(array $node, array $context, $expected)
    {
        $evaluated = $this->evaluator->evaluate(new Node($node), $context);
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider keyNoValueProvider
     */
    public function testEvaluatesKeyNoValue(array $node, array $context, $expected)
    {
        $evaluated = $this->evaluator->evaluate(new Node($node), $context);
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider keyDefaultValueProvider
     */
    public function testEvaluatesKeyWithDefaultValue(array $node, array $context, $expected)
    {
        $evaluated = $this->evaluator->evaluate(new Node($node), $context);
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider nestedContextProvider
     */
    public function testNestedContext(array $node, array $context, $expected)
    {
        $evaluated = $this->evaluator->evaluate(new Node($node), $context);
        $this->assertEquals($expected, $evaluated);
    }    

    public function absentKeyProvider()
    {
        return [
            [
                [
                    'type' => ParsesFloip::MEMBER_TYPE,
                    'key' => 'contact',
                    'value' => 'name',
                    'location' => [
                        'start' => [
                            'offset' => 6,
                        ],
                        'end' => [
                            'offset' => 19
                        ]
                    ]
                ],
                [
                    // empty context
                ],
                'contact.name'
            ],
        ];
    }

    public function arrayReturnProvider()
    {
        return [
            [
                [
                    'type' => ParsesFloip::MEMBER_TYPE,
                    'key' => 'flow',
                    'value' => 'multipleChoice.value',
                    'location' => [
                        'start' => [
                            'offset' => 6,
                        ],
                        'end' => [
                            'offset' => 19
                        ]
                    ]
                ],
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
                [
                    'type' => ParsesFloip::MEMBER_TYPE,
                    'key' => 'contact',
                    'value' => 'name',
                    'location' => [
                        'start' => [
                            'offset' => 6,
                        ],
                        'end' => [
                            'offset' => 19
                        ]
                    ]
                ],
                [
                    'contact' => [
                        'name' => 'Kyle',
                    ]
                ],
                'Kyle'
            ],

            [
                [
                    'type' => ParsesFloip::MEMBER_TYPE,
                    'key' => 'contact',
                    'value' => 'name',
                    'location' => [
                        'start' => [
                            'offset' => 0,
                        ],
                        'end' => [
                            'offset' => 13
                        ]
                    ]
                ],
                [
                    'contact' => [
                        'name' => 'Kyle',
                    ]
                ],
                'Kyle'
            ],

            [
                [
                    'type' => ParsesFloip::MEMBER_TYPE,
                    'key' => 'contact',
                    'value' => 'name',
                    'location' => [
                        'start' => [
                            'offset' => 4,
                        ],
                        'end' => [
                            'offset' => 17
                        ]
                    ]
                ],
                [
                    'contact' => [
                        'name' => 'Kyle',
                    ]
                ],
                'Kyle'
            ],

            [
                [
                    'type' => ParsesFloip::MEMBER_TYPE,
                    'key' => 'contact',
                    'value' => 'name',
                    'location' => [
                        'start' => [
                            'offset' => 4,
                        ],
                        'end' => [
                            'offset' => 19
                        ]
                    ]
                ],
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
                [
                    'type' => ParsesFloip::MEMBER_TYPE,
                    'key' => 'contact',
                    'value' => null,
                    'location' => [
                        'start' => [
                            'offset' => 6,
                        ],
                        'end' => [
                            'offset' => 19
                        ]
                    ]
                ],
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
                [
                    'type' => ParsesFloip::MEMBER_TYPE,
                    'key' => 'contact',
                    'value' => null,
                    'location' => [
                        'start' => [
                            'offset' => 6,
                        ],
                        'end' => [
                            'offset' => 19
                        ]
                    ]
                ],
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
                [
                    'type' => ParsesFloip::MEMBER_TYPE,
                    'key' => 'contact',
                    'value' => 'lang.default',
                    'location' => [
                        'start' => [
                            'offset' => 6,
                        ],
                        'end' => [
                            'offset' => 27
                        ]
                    ]
                ],
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
                [
                    'type' => ParsesFloip::MEMBER_TYPE,
                    'key' => 'contact',
                    'value' => 'address.business.city',
                    'location' => [
                        'start' => [
                            'offset' => 6,
                        ],
                        'end' => [
                            'offset' => 27
                        ]
                    ]
                ],
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
                [
                    'type' => ParsesFloip::MEMBER_TYPE,
                    'key' => 'contact',
                    'value' => 'address.business.city',
                    'location' => [
                        'start' => [
                            'offset' => 6,
                        ],
                        'end' => [
                            'offset' => 27
                        ]
                    ]
                ],
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
                [
                    'type' => ParsesFloip::MEMBER_TYPE,
                    'key' => 'contact',
                    'value' => 'address.business.city',
                    'location' => [
                        'start' => [
                            'offset' => 6,
                        ],
                        'end' => [
                            'offset' => 27
                        ]
                    ]
                ],
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
        ];
    }
}
