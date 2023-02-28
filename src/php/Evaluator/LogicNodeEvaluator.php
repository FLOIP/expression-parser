<?php

namespace Viamo\Floip\Evaluator;

use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\Exception\NodeEvaluatorException;
use function strtolower;
use function strtoupper;

class LogicNodeEvaluator extends AbstractNodeEvaluator
{
    public function evaluate(Node $node, $context): bool {
        if (!isset($node['rhs'], $node['lhs'], $node['operator'])) {
            throw new NodeEvaluatorException('Logic node is the wrong shape, should have "rhs", "lhs", "operator"');
        }
        $lhs = $this->value($node['lhs']);
        $rhs = $this->value($node['rhs']);
        $operator = $node['operator'];

        switch ($operator) {
            case '<':
                return $lhs < $rhs;
            case '<=':
                return $lhs <= $rhs;
            case '>':
                return $lhs > $rhs;
            case '>=':
                return $lhs >= $rhs;
            case '=':
                return $this->equals($lhs, $rhs);
            case '!=':
            case '<>':
                return $lhs !== $rhs;
        }
        throw new NodeEvaluatorException('invalid operator ' . $operator);
    }

    private function equals($lhs, $rhs) {
	    return $lhs === $rhs
		    // don't type juggle bools
		    || (($lhs == $rhs) && !is_bool($lhs) && !is_bool($rhs));
    }

    private function value($thing)
    {
        if ($thing instanceof Node) {
            $thing = $thing->getValue();
        }
        if (is_string($thing)) {
            if (strtoupper($thing) == 'TRUE') {
                return true;
            }
            if (strtoupper($thing) == 'FALSE') {
                return false;
            }
            if ($thing === '') {
                return null;
            }
            $thing = strtolower($thing);
        }
        return $thing;
    }

    public function handles(): string {
        return ParsesFloip::LOGIC_TYPE;
    }
}
