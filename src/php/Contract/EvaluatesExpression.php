<?php

namespace Viamo\Floip\Contract;

use Viamo\Floip\Evaluator\Node;

interface EvaluatesExpression
{
    /**
     * Given a node and a context, evaluate the output value of the node.
     *
     * @param Node $node
     * @param array $context
     * @return mixed
     */
    public function evaluate(Node $node, array $context);

    /**
     * Reports what type of node this evaluator handles.
     *
     * @return string
     */
    public function handles();
}
