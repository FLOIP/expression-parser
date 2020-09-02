<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Viamo\Floip\Evaluator\Exception\MethodNodeException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\ArrayHandler as ArrayHandlerInterface;
use Viamo\Floip\Evaluator\Node;

class ArrayHandler extends AbstractMethodHandler implements ArrayHandlerInterface
{
    public function handles() {
        return [
            'array',
            'in',
            'count',
        ];
    }

    public function _array() {
        // slice the args since the last is the eval context
        return array_map([$this, 'value'], array_slice(func_get_args(), 0, -1));
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
