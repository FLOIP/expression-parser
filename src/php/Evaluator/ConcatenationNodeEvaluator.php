<?php

namespace Viamo\Floip\Evaluator;

use Viamo\Floip\Evaluator\Node;
use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\AbstractNodeEvaluator;
use Viamo\Floip\Evaluator\Exception\NodeEvaluatorException;

class ConcatenationNodeEvaluator extends AbstractNodeEvaluator
{
    public function evaluate(Node $node, array $context)
    {
        if (!isset($node['rhs'], $node['lhs'])) {
            throw new NodeEvaluatorException('Concatenation node is the wrong shape, should have "rhs", "lhs", "operator"');
        }
        $lhs = $this->value($node['lhs']);
        $rhs = $this->value($node['rhs']);

        return $lhs . $rhs;
    }

    private function value($thing)
    {
        if ($thing instanceof Node) {
            $thing = $thing->getValue();
        }
        return $thing;
    }

    public function handles()
    {
        return ParsesFloip::CONCATENATION_TYPE;
    }
}
