<?php

namespace Viamo\Floip\Evaluator;

use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\EvaluatesMethods;
use Viamo\Floip\Evaluator\Exception\NodeEvaluatorException;
use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\Exception\MethodNodeException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Chainable;

/**
 * Evaluates METHOD nodes -- expressions that look like @(FUNC(arg...))
 */
class MethodNodeEvaluator extends AbstractNodeEvaluator
{
    /** @var array */
    private $handlers = [];

    /**
     * Add a handler for specific method types.
     * For example, method types dealing with dates might be contained
     * in a separate DateTime handler that reports [year(), now()] as methods
     * it knows how to evaluate.
     *
     * @param EvaluatesMethods $handler
     * @return MethodNodeEvaluator
     */
    public function addHandler(EvaluatesMethods $handler)
    {
        foreach ($handler->handles() as $method) {
            $this->handlers[\strtolower($method)] = $handler;
        }
        return $this;
    }

    /**
     * Get the handler associated with a particular method.
     *
     * @param string $method
     * @return EvaluatesMethods
     */
    public function getHandler($method)
    {
        if (isset($this->handlers[$method])) {
            return $this->handlers[$method];
        }
        throw new NodeEvaluatorException('No node method handler found for ' . $method);
    }

    /**
     * Evaluate the method call node with the given context.
     *
     * @param Node $node
     * @param array|ArrayAccess $context
     * @return mixed
     */
    public function evaluate(Node $node, $context)
    {
        if (!isset($node['call'], $node['args']) || !is_array($node['args'])) {
            throw new NodeEvaluatorException('Method node is the wrong shape, should have "call", "args"');
        }
        $call = $node['call'];
        $args = $node['args'];
        // transform any child nodes to their value
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

        $result = call_user_func_array([$handler, $call], $args);

        if (isset($node['chain'])) {
            foreach ($node['chain'] as $chain) {
                if ($result instanceof Chainable) {
                    $result = $result->chain($chain);
                } else {
                    throw new MethodNodeException("Must chain on Chainable object, got " . \gettype($result));
                }
            }
        }

        return $result;
    }

    public function handles()
    {
        return ParsesFloip::METHOD_TYPE;
    }
}
