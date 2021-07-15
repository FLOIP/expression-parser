<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\TestResult as TestResultInterface;

class TestResult implements TestResultInterface
{
    /** @var mixed $value */
    private $value;

    /** @var mixed $match */
    private $match;

    public function __construct($value, $match) {
        $this->value = $value;
        $this->match = $match;
    }
    
    public function getMatch() {
        return (string) $this->match;
    }

    public function getValue() {
        return (string) $this->value;
    }

    public function __toString() {
        return $this->getValue();
    }
}
