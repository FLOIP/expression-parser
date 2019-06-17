<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Math as MathInterface;

class Math extends AbstractMethodHandler implements MathInterface
{
    // php 5.5 compat
    const PHP_INT_MIN = -9223372036854775808;
    public function abs($number)
    {
        return abs($number);
    }
    public function max()
    {
        $args = \func_get_args();
        return array_reduce($args, 'max', static::PHP_INT_MIN);
    }
    public function min()
    {
        $args = \func_get_args();
        return array_reduce($args, 'min', \PHP_INT_MAX);
    }
    public function power($number, $power)
    {
        return pow($number, $power);
    }
    public function sum()
    {
        $args = \func_get_args();
        return array_sum($args);
    }
}
