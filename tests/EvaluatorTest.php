<?php

namespace Viamo\Floip\Tests;

use Viamo\Floip\Tests\TestCase;
use Viamo\Floip\Evaluator;
use Viamo\Floip\Parser;
use Viamo\Floip\Contract\ParsesFloip;
use Mockery;
use Mockery\MockInterface;
use Viamo\Floip\Contract\EvaluatesExpression;

class EvaluatorTest extends TestCase
{

    protected ParsesFloip|Mockery\LegacyMockInterface|Parser|MockInterface $parser;
    protected Evaluator $evaluator;

    public function setUp(): void
    {
        $this->parser = Mockery::mock(Parser::class);
        $this->evaluator = new Evaluator($this->parser);
        parent::setUp();
    }

    public function testCanAddHandler(): void {
        $type = 'foobar';
        $handler = Mockery::mock(EvaluatesExpression::class);
        $handler->shouldReceive('handles')->andReturn($type);
        $this->evaluator->addNodeEvaluator($handler);
        $this->parser->shouldReceive('parse')
            ->andReturn([['type' => $type]]);

        $result = $this->evaluator->getNodeEvaluator($type);

        $this->assertEquals($handler, $result);
    }

    public function testCallsHandlerForType(): void {
        // arrange
        $type = 'foobar';
        $expected = '42';

        $handler = Mockery::mock(EvaluatesExpression::class);
        $handler->shouldReceive('handles')->andReturn($type);
        $handler->shouldReceive('evaluate')->andReturn($expected);

        $this->evaluator->addNodeEvaluator($handler);
        $this->parser->shouldReceive('parse')
            ->andReturn([[
                'type' => $type,
                'location' => [
                    'start' => [
                        'offset' => 0,
                    ],
                    'end' => [
                        'offset' => 4,
                    ]
                ]
                ]]);

        // act
        $result = $this->evaluator->evaluate('test', []);

        // assert
        $this->assertEquals($expected, $result);
    }
}
