<?php

namespace Floip\Tests\Evaluator;

use PHPUnit\Framework\TestCase;
use Floip\Evaluator\MemberEvaluator;
use Floip\Contract\ParsesFloip;
use Floip\Evaluator\Node;

class MemberEvaluatorTest extends TestCase
{
    /** @var MemberEvaluator */
    private $evaluator;

    public function setUp()
    {
        $this->evaluator = new MemberEvaluator;
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
}
