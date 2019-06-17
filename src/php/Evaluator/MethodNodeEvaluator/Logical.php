<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Logical as LogicalInterface;
use Viamo\Floip\Evaluator\Exception\MethodNodeException;

/**
 * @method and
 * @method if
 * @method or
 */
class Logical extends AbstractMethodHandler implements LogicalInterface
{
    public function _and()
    {
        foreach (\func_get_args() as $arg) {
            if ($arg == false) {
                return false;
            }
        }
        return true;
    }
    public function _if()
    {
        $args = \func_get_args();
        if (count($args) != 3) {
            throw new MethodNodeException('Too many args for if: ', \func_num_args());
        }
        return $args[0] ? $args[1] : $args[2];
    }
    public function _or()
    {
        foreach (\func_get_args() as $arg) {
            if ($arg == true) {
                return true;
            }
        }
        return false;
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
    public function handles()
    {
        return ['and', 'or', 'if'];
    }
}
