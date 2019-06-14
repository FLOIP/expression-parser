<?php

namespace Floip\Evaluator\MethodEvaluator;

use Floip\Evaluator\MethodEvaluator\Contract\Logical as LogicalInterface;

class Logical extends AbstractMethodHandler implements LogicalInterface
{
    public function and(array $args)
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
        //
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
