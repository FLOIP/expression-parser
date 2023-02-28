<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;

interface Math extends EvaluatesMethods
{
    /**
     * Returns the absolute value of a number
     */
    public function abs(int|float $number): float|int;
    
    /**
     * Returns the maximum value of all arguments
     */
    public function max(): float|int;

    /**
     * Returns the minimum value of all arguments
     */
    public function min(): float|int;

    /**
     * Returns the result of a number raised to a power - equivalent to the ^ operator
     */
    public function power(int|float $number, int|float $power): float|int;

    /**
     * Returns the sum of all arguments, equivalent to the + operator
     */
    public function sum(): float|int;
}
