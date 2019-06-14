<?php

namespace Floip\Evaluator\MethodEvaluator\Contract;

interface Math extends EvaluatesMethods
{
    /**
     * Returns the absolute value of a number
     *
     * @param int|float $number
     * @return int|float
     */
    public function abs($number);
    
    /**
     * Returns the maximum value of all arguments
     *
     * @param int[]|float[] $args
     * @return int|float
     */
    public function max(array $args);

    /**
     * Returns the minimum value of all arguments
     *
     * @param int[]|float[] $args
     * @return int|float
     */
    public function min(array $args);

    /**
     * Returns the result of a number raised to a power - equivalent to the ^ operator
     *
     * @param int|float $number
     * @param int|float $power
     * @return int|float
     */
    public function power($number, $power);

    /**
     * Returns the sum of all arguments, equivalent to the + operator
     *
     * @param int[]|float[] $args
     * @return int|float
     */
    public function sum(array $args);
}
