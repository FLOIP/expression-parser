<?php

namespace Floip\Evaluator\MethodEvaluator;

use Floip\Evaluator\MethodEvaluator\Contract\Logical as LogicalInterface;

class Logical extends AbstractMethodHandler implements LogicalInterface
{
    public function and()
    {
        foreach (\func_get_args() as $arg) {
            if ($arg == false) {
                return false;
            }
        }
        return true;
    }
    public function if()
    {
        $args = \func_get_args();
        if (count($args) != 3) {
            throw new \Exception('Too many args for if');
        }
        return $args[0] ? $args[1] : $args[2];
    }
    public function or()
    {
        foreach (\func_get_args() as $arg) {
            if ($arg == true) {
                return true;
            }
        }
        return false;
    }
}
