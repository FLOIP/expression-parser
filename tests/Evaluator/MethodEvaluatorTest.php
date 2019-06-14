<?php

namespace Viamo\Floip\Tests\Evaluator;

use PHPUnit\Framework\TestCase;
use Viamo\Floip\Evaluator\MethodEvaluator;
use Viamo\Floip\Evaluator\MethodEvaluator\Contract\EvaluatesMethods;
use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\Node;

class MethodEvaluatorTest extends TestCase
{
    /** @var MethodEvaluator */
    private $evaluator;

    /** @var \Mockery\MockInterface */
    private $handler;

    public function setUp()
    {
        $this->evaluator = new MethodEvaluator;
        $this->handler = \Mockery::mock(EvaluatesMethods::class);
        $this->handler->shouldReceive('handles')->andReturn(['foo']);
    }

    public function testCanAddHandler()
    {
        $handler = $this->handler;
        $this->evaluator->addHandler($handler);
        
        $this->assertEquals($handler, $this->evaluator->getHandler('foo'));
    }

    public function testHandlerIsUsedToEvaluate()
    {
        $handler = $this->handler;
        $handler->shouldReceive('foo')->once()->andReturn('foobar');
        $this->evaluator->addHandler($handler);

        $node = [
            'type' => ParsesFloip::METHOD_TYPE,
            'call' => 'FOO',
            'args' => [],
            'location' => [
                'start' => [
                    'offset' => 4
                ],
                'end' => [
                    'offset' => 12
                ]
            ]
        ];
        $context = [];

        $evaluated = $this->evaluator->evaluate(new Node($node), $context);

        $this->assertEquals('foobar', $evaluated);
    }

    public function testHandlerIsPassedArgs()
    {
        $handler = $this->handler;
        $handler->shouldReceive('foo')
            ->once()
            ->withArgs(['foo', 'bar'])
            ->andReturn('foobar');
        $this->evaluator->addHandler($handler);

        $node = [
            'type' => ParsesFloip::METHOD_TYPE,
            'call' => 'FOO',
            'args' => ["foo", "bar"],
            'location' => [
                'start' => [
                    'offset' => 4
                ],
                'end' => [
                    'offset' => 9
                ]
            ]
        ];
        $context = [];

        $this->evaluator->evaluate(new Node($node), $context);
    }
}
