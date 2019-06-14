<?php

namespace Floip\Evaluator;

use Floip\Evaluator\MethodEvaluator\DateTime;
use Floip\Evaluator\MethodEvaluator\Excellent;
use Floip\Evaluator\MethodEvaluator\Logical;
use Floip\Evaluator\MethodEvaluator\Math;
use Floip\Evaluator\MethodEvaluator\Text;
use Floip\Evaluator\MethodEvaluator\Contract\EvaluatesMethods;

class MethodEvaluator extends AbstractNodeEvaluator
{
    private $handlers = [];

    public function addHandler(EvaluatesMethods $handler)
    {
        foreach ($handler->handles() as $method) {
            $this->handlers[\strtolower($method)] = $handler;
        }
    }

    public function getHandler($method)
    {
        if (isset($this->handlers[$method])) {
            return $this->handlers[$method];
        }
        throw new \Exception('No handler found for ' . $method);
    }

    public function evaluate($string, Node $node, array $context)
    {
        if (!isset($node['call'], $node['args']) || !is_array($node['args'])) {
            throw new \Exception;
        }
        $call = $node['call'];
        $args = $node['args'];
        $args = array_map(function ($arg) {
            if ($arg instanceof Node) {
                return $arg->getValue();
            }
            return $arg;
        }, $args);
        // if the call is in snake case, strip out the '_'
        \str_replace('_', '', $call);
        $call = \strtolower($call);

        $handler = $this->getHandler($call);
        return call_user_func_array([$handler, $call], $args);
    }
}
