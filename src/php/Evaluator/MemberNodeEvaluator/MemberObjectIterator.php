<?php

namespace Viamo\Floip\Evaluator\MemberNodeEvaluator;

use ArrayIterator;

/**
 * This iterator will expose the '__value__' of child expression objects.
 */
class MemberObjectIterator extends ArrayIterator
{
    public function current(): mixed {
        $current = parent::current();
        if (\is_array($current) && array_key_exists('__value__', $current)) {
            return $current['__value__'];
        }
        return $current;
    }
}
