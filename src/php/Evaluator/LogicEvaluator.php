<?php

namespace Viamo\Floip\Evaluator;

class LogicEvaluator extends AbstractNodeEvaluator 
{
    public function evaluate(Node $node, array $context)
    {
        if (!isset($node['rhs'], $node['lhs'], $node['operator'])) {
            throw new \Exception;
        }
        $lhs = $this->value($node['lhs']);
        $rhs = $this->value($node['rhs']);
        $operator = $node['operator'];

        switch($operator) {
            case '<':
                return $lhs < $rhs;
            case '<=':
                return $lhs <= $rhs;
            case '>':
                return $lhs > $rhs;
            case '>=':
                return $lhs >= $rhs;
            case '=':
                return $lhs == $rhs;
        }
        throw new \Exception('invalid operator ' . $operator);
    }

    private function value($thing)
    {
        if ($thing instanceof Node) {
            $thing = $thing->getValue();
        }
        if (is_string($thing)) {
            if (\strtoupper($thing) == 'TRUE') {
                return true;
            }
            if (\strtoupper($thing) == 'FALSE') {
                return false;
            }
        }
        return $thing;
    }
}
