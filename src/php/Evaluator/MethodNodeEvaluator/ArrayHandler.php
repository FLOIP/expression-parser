<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Viamo\Floip\Evaluator\Exception\MethodNodeException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\ArrayHandler as ArrayHandlerInterface;
use Viamo\Floip\Evaluator\Node;

class ArrayHandler extends AbstractMethodHandler implements ArrayHandlerInterface
{
    public function _array() {
        return array_map([$this, 'value'], func_get_args());
    }

    public function in($value, $array) {
        if ($array instanceof Node) {
            $array = $array->getValue();
        }
        if (!is_array($array)) {
            $type = \gettype($array);
            throw new MethodNodeException("Can only perform IN on an array, got $type");
        }
        return in_array($value, $array);
    }

    public function handles() {
        return [
            'array',
            'in',
			'count',
        ];
    }

    public function __call($name, array $args) {
        switch ($name) {
            case 'array':
                return call_user_func_array([$this, "_$name"], $args);
            default:
                if (in_array($name, $this->handles())) {
                    return call_user_func_array([$this, $name], $args);
                }
        }
        \trigger_error('Call to undefined method ' . static::class . '::' . $name . '()', \E_USER_ERROR);
    }

    
	public function count($array) {
		if ($array instanceof Node) {
			$array = $array->getValue();
		}
		if (!is_array($array)) {
			$type = \gettype($array);
			throw new MethodNodeException("Can only perform COUNT on an array, got $type");
		}
		return count($array);
	}
}
