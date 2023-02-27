<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;

interface Math extends EvaluatesMethods
{
    /**
     * Returns the absolute value of a number
     *
     * @param int|float $number
     * @return int|float
     */
    public function abs($number): float|int;
    
    /**
     * Returns the maximum value of all arguments
     *
     * @param int|float ...$value
     * @return int|float
     */
	public function max(): float|int;

    /**
     * Returns the minimum value of all arguments
     *
     * @param int|float ...$value
     * @return int|float
     */
	public function min(): float|int;

    /**
     * Returns the result of a number raised to a power - equivalent to the ^ operator
     *
     * @param int|float $number
     * @param int|float $power
     * @return int|float
     */
	public function power($number, $power): float|int;

    /**
     * Returns the sum of all arguments, equivalent to the + operator
     *
     * @param int|float ...$value
     * @return int|float
     */
	public function sum(): float|int;
}
