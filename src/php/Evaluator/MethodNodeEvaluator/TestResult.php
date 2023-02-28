<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Stringable;
use Viamo\Floip\Evaluator\Exception\MethodNodeException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\TestResult as TestResultInterface;

class TestResult implements TestResultInterface, Stringable
{
    public function __construct(
        private mixed $value = false,
        private mixed $match = null
    ) {
    }

    public function chain($method): string {
        return match ($method) {
            'value' => $this->getValue(),
            'match' => $this->getMatch(),
            default => throw new MethodNodeException("Unknown chain method $method on TestResult"),
        };
    }
    
    public function getMatch(): string {
        return (string) $this->match;
    }

    public function getValue(): string {
        if ($this->value === true) {
            return 'TRUE';
        } else if ($this->value === false) {
            return 'FALSE';
        } else {
            return (string) $this->value;
        }
    }

    public function __toString(): string {
        return $this->getValue();
    }
}
