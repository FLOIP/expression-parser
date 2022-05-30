<?php

namespace Viamo\Floip\Providers;

use Viamo\Floip\Parser;
use Viamo\Floip\Evaluator;
use Viamo\Floip\Contract\ParsesFloip;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;
use Viamo\Floip\Evaluator\BoolNodeEvaluator;
use Viamo\Floip\Evaluator\LogicNodeEvaluator;
use Viamo\Floip\Evaluator\MemberNodeEvaluator;
use Viamo\Floip\Evaluator\MethodNodeEvaluator;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Math;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Text;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Logical;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\DateTime;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Excellent;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Math as MathInterface;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Text as TextInterface;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Logical as LogicalInterface;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\DateTime as DateTimeInterface;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Excellent as ExcellentInterface;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\ArrayHandler as ArrayInterface;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\MatchTest as MatchTestInterface;
use Viamo\Floip\Evaluator\EscapeNodeEvaluator;
use Viamo\Floip\Evaluator\ConcatenationNodeEvaluator;
use Viamo\Floip\Evaluator\MathNodeEvaluator;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\ArrayHandler;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\MatchTest;
use Viamo\Floip\Evaluator\NullNodeEvaluator;

class ExpressionEvaluatorServiceProvider extends ServiceProvider
{
    /** @var Container */
    protected $app;
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(ParsesFloip::class, Parser::class);

        $this->registerMethodNodeHandlers();

        $this->registerMethodNodeEvaluator();

        $this->registerEvaluator();
    }

    protected function registerEvaluator()
    {
        $this->app->singleton(Evaluator::class, function (Container $app) {
            $eval = new Evaluator($app->make(ParsesFloip::class));
            foreach ($this->getNodeEvaluators() as $nodeHandler) {
                $eval->addNodeEvaluator($nodeHandler);
            }

            return $eval;
        });
    }

    protected function registerMethodNodeEvaluator()
    {
        $this->app->bind(MethodNodeEvaluator::class, function (Container $app) {
            $eval = new MethodNodeEvaluator;
            foreach ($this->getMethodHandlers() as $methodHandler) {
                $eval->addHandler($methodHandler);
            }

            return $eval;
        });
    }


    protected function registerMethodNodeHandlers()
    {
        $this->app->bind(DateTimeInterface::class, DateTime::class);
        $this->app->bind(ExcellentInterface::class, Excellent::class);
        $this->app->bind(LogicalInterface::class, Logical::class);
        $this->app->bind(MathInterface::class, Math::class);
        $this->app->bind(TextInterface::class, Text::class);
        $this->app->bind(ArrayInterface::class, ArrayHandler::class);
        $this->app->bind(MatchTestInterface::class, MatchTest::class);
    }

    protected function getMethodHandlers()
    {
        return array_map(function ($interface) {
            return $this->app->make($interface);
        }, [
            DateTimeInterface::class,
            ExcellentInterface::class,
            LogicalInterface::class,
            MathInterface::class,
            TextInterface::class,
            ArrayInterface::class,
            MatchTestInterface::class,
        ]);
    }

    protected function getNodeEvaluators()
    {
        return [
            new LogicNodeEvaluator,
            new MemberNodeEvaluator,
            new EscapeNodeEvaluator,
            new ConcatenationNodeEvaluator,
            new MathNodeEvaluator,
            new BoolNodeEvaluator,
            new NullNodeEvaluator,
            $this->app->make(MethodNodeEvaluator::class)
        ];
    }

    public function provides()
    {
        return [Evaluator::class, ParsesFloip::class, MethodNodeEvaluator::class];
    }
}
