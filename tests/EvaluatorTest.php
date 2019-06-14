<?php

namespace Floip\Tests;

use PHPUnit\Framework\TestCase;
use Floip\Evaluator;
use Floip\Parser;
use Mockery;
use Mockery\MockInterface;
use Floip\Contract\EvaluatesExpression;

class EvaluatorTest extends TestCase
{

    /** @var MockInterface  */
    protected $parser;
    /** @var Evaluator */
    protected $evaluator;

    public function setUp()
    {
        $this->parser = Mockery::mock(Parser::class);
        $this->evaluator = new Evaluator($this->parser);
    }

    public function testCanAddHandler()
    {
        $type = 'foobar';
        $handler = Mockery::mock(EvaluatesExpression::class);
        $this->evaluator->addNodeEvaluator($handler, $type);
        $this->parser->shouldReceive('parse')
            ->andReturn([['type' => $type]]);

        $result = $this->evaluator->getNodeEvaluator($type);

        $this->assertEquals($handler, $result);
    }

    public function testCallsHandlerForType()
    {
        // arrange
        $type = 'foobar';
        $expected = '42';

        $handler = Mockery::mock(EvaluatesExpression::class);
        $handler->shouldReceive('evaluate')->andReturn($expected);

        $this->evaluator->addNodeEvaluator($handler, $type);
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
