<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Math as MathInterface;
use function func_get_args;
use const PHP_INT_MAX;

class Math extends AbstractMethodHandler implements MathInterface
{
    // php 5.5 compat
    const PHP_INT_MIN = -9223372036854775808;
    public function abs($number): float|int {
        return abs($this->value($number));
    }
    
    public function max(): float|int {
        $args = array_filter(array_map([$this, 'value'], func_get_args()), 'is_numeric');
        return array_reduce($args, 'max', static::PHP_INT_MIN);
    }
    
    public function min(): float|int {
        $args = array_filter(array_map([$this, 'value'], func_get_args()), 'is_numeric');
        return array_reduce($args, 'min', PHP_INT_MAX);
    }
    
    public function power($number, $power): float|int {
        return $this->value($number) ** $this->value($power);
    }
    
    public function sum(): float|int {
        $args = array_map([$this, 'value'], func_get_args());
        return array_sum($args);
    }
}
