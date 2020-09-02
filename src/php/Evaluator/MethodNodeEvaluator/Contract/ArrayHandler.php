<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;

interface ArrayHandler extends EvaluatesMethods
{
    /**
     * Cast a series of values to an array.
     *
     * @param mixed ...$args
     * @param array $context
     * @return array
     */
    public function _array();

    /**
     * Determine whether a value is contained within an array.
     *
     * @param mixed $value
     * @param array $array
     * @return bool
     */
    public function in($value, $array);

	/**
	 * Count the number of elements in an array
	 *
	 * @param $array
	 * @return int
	 */
	public function count($array);
}
