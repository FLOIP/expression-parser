<?php

namespace Viamo\Floip\Evaluator\MemberNodeEvaluator;

use ArrayObject;
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
        return \json_encode($this, \JSON_FORCE_OBJECT);
    }

    public function getIterator() {
        return new MemberObjectIterator($this);
    }

    public function &offsetGet($index) {
        $item = array_key_exists($index, $this->data) ? $this->data[$index] : null;
        if (is_array($item) && \array_key_exists('__value__', $item)) {
            return $item['__value__'];
        }
        return $item;
    }

    public function jsonSerialize() {
        return $this->getArrayCopy();
    }
}
