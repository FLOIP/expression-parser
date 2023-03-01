<?php

namespace Viamo\Floip\Tests;

use Viamo\Floip\Evaluator;
use Orchestra\Testbench\TestCase;
use Viamo\Floip\Providers\ExpressionEvaluatorServiceProvider;

class EvaluatorLaravelIntegrationTest extends TestCase
{
    protected function getPackageProviders($app): array {
        return [ExpressionEvaluatorServiceProvider::class];
    }

    /**
     * @dataProvider expressionProvider
     */
    public function testEvaluatesExpression($expected, $string, array $context)
    {
        $evaluator = $this->app->make(Evaluator::class);

        $this->assertEquals($expected, $evaluator->evaluate($string, $context));
    }

    public function expressionProvider(): array {
        return [
            [
                'Hello John Smith, 2 = 2, 3, TRUE, @world',
                'Hello @(contact.firstname & " " & contact.lastname), @(1 + 1) = 2, @(max(2,3)), @(1 < 3), @@world',
                ['contact' => ['firstname' => 'John', 'lastname' => 'Smith']],
            ],
        ];
    }
}
