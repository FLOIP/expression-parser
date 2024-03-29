<?php

namespace Viamo\Floip\Evaluator;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateInterval;
use Exception;
use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\Exception\NodeEvaluatorException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\DateTime;
use function is_numeric;
use function preg_match;

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
        return match ($operator) {
            '+' => $lhs + $rhs,
            '-' => $lhs - $rhs,
            '/' => $lhs / $rhs,
            '*' => $lhs * $rhs,
            '^' => $lhs ** $rhs,
            default => throw new NodeEvaluatorException('invalid operator ' . $operator),
        };
    }

    private function evaluateDates($lhs, $rhs, $operator) {
        if (!($lhs instanceof Carbon)) {
            throw new NodeEvaluatorException('When performing date math, left hand side must be a date');
        }
        if (!($rhs instanceof DateInterval)) {
            $rhs = CarbonInterval::createFromDateString($rhs);
            // throw new NodeEvaluatorException('When performing date math, right hand side must be a time interval');
        }
        return match ($operator) {
            '+' => $lhs->add($rhs),
            '-' => $lhs->sub($rhs),
            default => throw new NodeEvaluatorException('invalid operator for date math: ' . $operator),
        };
    }

    private function isDateValue($thing): bool {
        return $thing instanceof Carbon || $thing instanceof CarbonInterval;
    }

    private function value($thing)
    {
        if ($thing instanceof Node) {
            $thing = $thing->getValue();
        }
        try {
            if (!is_numeric($thing) && !($this->isDateValue($thing))) {
                return $this->parseDateTime($thing);
            }
        } catch (Exception $e) {
            throw new NodeEvaluatorException("Can only perform math on numbers, got: '$thing'", 0, $e);
        }
        return $thing;
    }

    private function parseDateTime($thing): CarbonInterval|Carbon {
        // does this look like a date interval string? e.g. "4 days"
        if (preg_match(DateTime::DATE_INTERVAL_REGEX, (string) $thing) === 1) {
            return CarbonInterval::createFromDateString($thing);
        }
        // otherwise try parsing it as a datetime
        return Carbon::parse($thing);

    }

    public function handles(): string {
        return ParsesFloip::MATH_TYPE;
    }
}
