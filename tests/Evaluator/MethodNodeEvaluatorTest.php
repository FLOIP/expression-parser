<?php

namespace Viamo\Floip\Tests\Evaluator;

use Mockery;
use Mockery\MockInterface;
use Viamo\Floip\Tests\TestCase;
use Viamo\Floip\Evaluator\MethodNodeEvaluator;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\EvaluatesMethods;
use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\Node;

class MethodNodeEvaluatorTest extends TestCase
{
    /** @var MethodNodeEvaluator */
    private MethodNodeEvaluator $evaluator;

    /** @var MockInterface */
    private Mockery\LegacyMockInterface|EvaluatesMethods|MockInterface $handler;

    public function setUp(): void
    {
        $this->evaluator = new MethodNodeEvaluator;
        $this->handler = Mockery::mock(EvaluatesMethods::class);
        $this->handler->shouldReceive('handles')->andReturn(['foo']);
        parent::setUp();
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

        // a failing test throws an exception
        $this->assertTrue(true);
    }
}
