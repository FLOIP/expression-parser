<?php

namespace Viamo\Floip\Evaluator\MemberNodeEvaluator;

use ArrayAccess;
use ArrayObject;
use Illuminate\Support\Arr;
use JsonSerializable;

/**
 * Holds expression objects for further evaluation.
 * This allows the object to be used as an object in certain evaluation contexts
 * instead of just as a string.
 * When coerced into a string, becomes a JSON string.
 */
class MemberObject extends ArrayObject implements JsonSerializable
{
    public function __toString() {
        return (string) \json_encode($this, \JSON_FORCE_OBJECT);
    }

    public function getIterator(): MemberObjectIterator {
        return new MemberObjectIterator($this);
    }

    public function &offsetGet($index): mixed {
        $item = parent::offsetGet($index);

        if (\is_array($item) || $item instanceof ArrayAccess) {
            if (Arr::has($item, '__value__')) {
                return $item['__value__'];
            }
        } else {
            return $item;
        }
    }

    public function jsonSerialize(): mixed {
        return $this->getArrayCopy();
    }
}
