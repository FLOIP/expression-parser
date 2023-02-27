<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Countable;
use Traversable;
use Viamo\Floip\Evaluator\Exception\MethodNodeException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\ArrayHandler as ArrayHandlerInterface;
use Viamo\Floip\Evaluator\Node;

class ArrayHandler extends AbstractMethodHandler implements ArrayHandlerInterface
{
    public function handles(): array {
	    return [
		    'array',
		    'in',
		    'count',
	    ];
    }
	
	public function _array(): array {
		return array_map([$this, 'value'], func_get_args());
	}

    protected function value($thing) {
        $thing = parent::value($thing);
        if (is_array($thing) && isset($thing['__value__'])) {
            return $thing['__value__'];
        }
        return $thing;
    }
	
	public function in($value, $array): bool {
		$value = $this->value($value);
		if ($array instanceof Node) {
			$array = $array->getValue();
		}
		if (!(is_array($array) || $array instanceof Traversable)) {
			$type = \gettype($array);
			throw new MethodNodeException("Can only perform IN on an array or Traversable, got $type");
		}
		// we can't just do in_array since we want to inspect the __value__ of
		// object-like values
        foreach ($array as $item) {
            $item = $this->value($item);
            if ($item == $value) {
                return true;
            }
        }
        return false;
    }
	
	public function count($array): int {
		if ($array instanceof Node) {
			$array = $array->getValue();
		}
		if (!(is_array($array) || $array instanceof Countable)) {
			$type = \gettype($array);
			throw new MethodNodeException("Can only perform COUNT on an array or Countable, got $type");
		}
		return count($array);
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
}
