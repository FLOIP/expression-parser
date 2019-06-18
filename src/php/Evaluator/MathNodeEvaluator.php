<?php

namespace Viamo\Floip\Evaluator;

use Viamo\Floip\Evaluator\Node;
use Viamo\Floip\Evaluator\Exception\NodeEvaluatorException;
use Viamo\Floip\Contract\ParsesFloip;

class MathNodeEvaluator extends AbstractNodeEvaluator
{
    public function evaluate(Node $node, array $context)
    {
        if (!isset($node['rhs'], $node['lhs'], $node['operator'])) {
            throw new NodeEvaluatorException('Math node is the wrong shape, should have "rhs", "lhs", "operator"');
        }
        $lhs = $this->value($node['lhs']);
        $rhs = $this->value($node['rhs']);
        $operator = $node['operator'];

        switch($operator) {
            case '+':
                return $lhs + $rhs;
            case '-':
                return $lhs - $rhs;
            case '/':
                return $lhs / $rhs;
            case '*':
                return $lhs * $rhs;
            case '^':
                return pow($lhs, $rhs);
        }
        throw new NodeEvaluatorException('invalid operator ' . $operator);
    }

    private function value($thing)
    {
        if ($thing instanceof Node) {
            $thing = $thing->getValue();
        }
        if (!\is_numeric($thing)) {
            throw new NodeEvaluatorException("Can only perform math on numbers, got: '$thing'");
        }
        return $thing;
    }

    public function handles()
    {
        return ParsesFloip::MATH_TYPE;
    }
}
