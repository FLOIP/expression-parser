<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Viamo\Floip\Evaluator\Exception\MethodNodeException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\AbstractMethodHandler;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Logical as LogicalInterface;
use Viamo\Floip\Evaluator\Node;

/**
 * @method and
 * @method if
 * @method or
 */
class Logical extends AbstractMethodHandler implements LogicalInterface
{
    public function _and(): bool {
	    foreach (array_map([$this, 'value'], func_get_args()) as $arg) {
		    if ($arg == false) {
			    return false;
		    }
	    }
	    return true;
    }
	
	public function _if(): mixed {
		$args = array_map([$this, 'value'], \func_get_args());
		if (count($args) != 3) {
			throw new MethodNodeException('Wrong number of args for if: ', \func_num_args());
		}
		return $args[0] ? $args[1] : $args[2];
	}
	
	public function _or(): bool {
		foreach (\array_map([$this, 'value'], func_get_args()) as $arg) {
			if ($arg == true) {
				return true;
			}
		}
		return false;
	}

    protected function value($thing)
    {
        if ($thing instanceof Node) {
            $thing = $thing->getValue();
            if (is_string($thing)) {
                switch (\strtoupper($thing)) {
                    case 'TRUE':
                        return true;
                    case 'FALSE':
                        return false;
                }
            }
        }
        switch ($thing) {
            case 'TRUE':
            case 'true':
                return true;
            case 'FALSE':
            case 'false':
                return false;
            default:
                return $thing;
        }
    }

    public function __call($name, array $args)
    {
        switch ($name) {
            case 'and':
            case 'or':
            case 'if':
                return call_user_func_array([$this, "_$name"], $args);
        }
        \trigger_error('Call to undefined method ' . static::class . '::' . $name . '()', \E_USER_ERROR);
    }

    /**
     * @inheritDoc
     * @return string[]
     */
	public function handles(): array {
		return ['and', 'or', 'if'];
	}
}
