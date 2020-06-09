<?php

namespace Viamo\Floip\Tests\Evaluator\Mocks;

use ArrayAccess;
use JsonSerializable;

class MockArrayContext implements ArrayAccess, JsonSerializable
{
    /** @var array */
    private $data = [];

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function offsetExists($offset) {
        return \array_key_exists($offset, $this->data);
    }
    public function offsetGet($offset) {
        $data = $this->data[$offset];
        if (is_array($data)) {
            foreach ($data as $datum) {
                if (is_array($datum)) {
                    return new self($data);
                }
            }
            return $data;
        }
        return $data;
    }
    public function offsetSet($offset, $value) {
        return $this->data[$offset] = $value;
    }
    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }
    public function jsonSerialize() {
        return $this->data;
    }
}
