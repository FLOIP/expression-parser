<?php

namespace Viamo\Floip\Evaluator;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateInterval;
use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\Exception\NodeEvaluatorException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\DateTime;

class MathNodeEvaluator extends AbstractNodeEvaluator
{
    public function evaluate(Node $node, $context): mixed {
        if (!isset($node['rhs'], $node['lhs'], $node['operator'])) {
            throw new NodeEvaluatorException('Math node is the wrong shape, should have "rhs", "lhs", "operator"');
        }
        $lhs = $this->value($node['lhs']);
        $rhs = $this->value($node['rhs']);
        $operator = $node['operator'];

        if ($this->isDateValue($rhs) || $this->isDateValue($lhs)) {
            return $this->evaluateDates((clone $lhs), $rhs, $operator);
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

    private function evaluateDates($lhs, $rhs, $operator) {
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
        try {
            if (!\is_numeric($thing) && !($this->isDateValue($thing))) {
                return $this->parseDateTime($thing);
            }
        } catch (\Exception $e) {
            throw new NodeEvaluatorException("Can only perform math on numbers, got: '$thing'", 0, $e);
        }
        return $thing;
    }

    private function parseDateTime($thing) {
        // does this look like a date interval string? e.g. "4 days"
        if (\preg_match(DateTime::DATE_INTERVAL_REGEX, $thing) === 1) {
            return CarbonInterval::createFromDateString($thing);
        }
        // otherwise try parsing it as a datetime
        return Carbon::parse($thing);

    }

    public function handles(): string {
        return ParsesFloip::MATH_TYPE;
    }
}
