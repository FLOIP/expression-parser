<?php

namespace Floip\Evaluator;

use Contract\Math as MathInterface;

class Math implements MathInterface
{
    public function abs($number)
    {
        return abs($number);
    }
    public function max(array $args)
    {
        return array_reduce($args, 'max', 0);
    }
    public function min(array $args)
    {
        return array_reduce($args, 'min', 0);
    }
    public function power($number, $power)
    {
        return pow($number, $power);
    }
    public function sum(array $args)
    {
        return array_sum($args);
    }
}
