<?php

namespace Viamo\Floip\Evaluator;

use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\Exception\NodeEvaluatorException;

class ConcatenationNodeEvaluator extends AbstractNodeEvaluator
{
    public function evaluate(Node $node, $context): mixed {
        if (!isset($node['rhs'], $node['lhs'])) {
            throw new NodeEvaluatorException('Concatenation node is the wrong shape, should have "rhs", "lhs"');
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

    public function handles(): string {
        return ParsesFloip::CONCATENATION_TYPE;
    }
}
