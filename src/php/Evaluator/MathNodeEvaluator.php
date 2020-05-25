<?php

namespace Viamo\Floip\Evaluator;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DateInterval;
use Viamo\Floip\Evaluator\Node;
use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\AbstractNodeEvaluator;
use Viamo\Floip\Evaluator\Exception\NodeEvaluatorException;

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

        if ($this->isDateValue($rhs) || $this->isDateValue($lhs)) {
            return $this->evaluateDates($lhs, $rhs, $operator);
        }

        switch ($operator) {
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

    private function evaluateDates(Carbon $lhs, $rhs, $operator) {
        if (!($lhs instanceof Carbon)) {
            throw new NodeEvaluatorException('When performing date math, left hand side must be a date');
        }
        if (!($rhs instanceof DateInterval)) {
            $rhs = CarbonInterval::createFromDateString($rhs);
            // throw new NodeEvaluatorException('When performing date math, right hand side must be a time interval');
        }
        switch ($operator) {
            case '+':
                return $lhs->add($rhs);
            case '-':
                return $lhs->sub($rhs);
        }
        throw new NodeEvaluatorException('invalid operator for date math: ' . $operator);
    }

    private function isDateValue($thing) {
        return $thing instanceof Carbon || $thing instanceof CarbonInterval;
    }

    private function value($thing)
    {
        if ($thing instanceof Node) {
            $thing = $thing->getValue();
        }
        if (!\is_numeric($thing) && !($this->isDateValue($thing))) {
            try {
                return CarbonPeriod::create($thing);
            } catch (\Exception $e) {
                throw new NodeEvaluatorException("Can only perform math on numbers, got: '$thing'");
            }
        }
        return $thing;
    }

    public function handles()
    {
        return ParsesFloip::MATH_TYPE;
    }
}
