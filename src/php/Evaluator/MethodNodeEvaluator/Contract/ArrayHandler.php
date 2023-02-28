<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;

use Countable;
use Viamo\Floip\Evaluator\Node;

interface ArrayHandler extends EvaluatesMethods
{
    /**
     * Cast a series of values to an array.
     *
     * @param mixed ...$args
     * @param array $context
     * @return array
     */
    public function _array(): array;

    /**
     * Determine whether a value is contained within an array.
     *
     * @param mixed $value
     * @param iterable|Node $array
     *
     * @return bool
     */
    public function in(mixed $value, Node|iterable $array): bool;

	/**
	 * Count the number of elements in an array
	 */
	public function count(Node|Countable|array $array): int;
}
