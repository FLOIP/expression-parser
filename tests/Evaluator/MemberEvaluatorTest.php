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
    public function testEvaluatesKeyAndValue($string, array $node, array $context, $expected)
    {
        $evaluated = $this->evaluator->evaluate($string, new Node($node), $context);
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider keyNoValueProvider
     */
    public function testEvaluatesKeyNoValue($string, array $node, array $context, $expected)
    {
        $evaluated = $this->evaluator->evaluate($string, new Node($node), $context);
        $this->assertEquals($expected, $evaluated);
    }

    /**
     * @dataProvider keyDefaultValueProvider
     */
    public function testEvaluatesKeyWithDefaultValue($string, array $node, array $context, $expected)
    {
        $evaluated = $this->evaluator->evaluate($string, new Node($node), $context);
        $this->assertEquals($expected, $evaluated);
    }

    public function keyAndValueProvider()
    {
        return [
            'end of string' => [
                'Hello @contact.name',
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

            'begin of string' => [
                '@contact.name how are you?',
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

            'middle of string' => [
                'Hey @contact.name what\'s up?',
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

            'middle of string parens' => [
                'Hey @(contact.name) what\'s up?',
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
            'end of string' => [
                'Hello @contact',
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
            'end of string' => [
                'Hello @contact',
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
