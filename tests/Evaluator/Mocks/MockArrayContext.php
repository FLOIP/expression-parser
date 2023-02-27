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

    public function offsetExists($offset): bool {
	    return \array_key_exists($offset, $this->data);
    }
	
	public function offsetGet($offset): mixed {
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
	
	public function offsetSet($offset, $value): void {
		$this->data[$offset] = $value;
	}
	
	public function offsetUnset($offset): void {
		unset($this->data[$offset]);
	}
	
	public function jsonSerialize(): mixed {
		return $this->data;
	}
}
