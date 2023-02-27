<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Viamo\Floip\Evaluator\Exception\MethodNodeException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\TestResult as TestResultInterface;

class TestResult implements TestResultInterface
{
    /** @var mixed $value */
    private $value;

    /** @var mixed $match */
    private $match;

    public function __construct($value = false, $match = null) {
        $this->value = $value;
        $this->match = $match;
    }

    public function chain($method): string {
	    switch ($method) {
		    case 'value':
			    return $this->getValue();
		    case 'match':
			    return $this->getMatch();
		    default:
			    throw new MethodNodeException("Unknown chain method $method on TestResult");
	    }
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

    public function __toString() {
        return $this->getValue();
    }
}
