<?php

namespace Viamo\Floip\Evaluator;

use ArrayAccess;
use Exception;
use JsonSerializable;
use Stringable;
use function implode;
use function is_array;

class Node implements ArrayAccess, Stringable, JsonSerializable {

    private array $data = [];

    private mixed $value = null;

    private bool $valueSet = false;

    public function __construct($data)
    {
        $this->data = array_map([$this, 'transformData'], $data);
    }

    public function jsonSerialize(): mixed {
        return $this->data;
    }

    /**
     * Recurse into a tree and transform node structures into Node objects.
     */
    public function transformData(mixed $data): mixed {
        if (static::isNode($data)) {
            return new self($data);
        }
        if (is_array($data)) {
            return array_map([$this, 'transformData'], $data);
        }
        return $data;
    }

    /**
     * Set a value that represents the node.
     */
    public function setValue(mixed $value): Node {
        if ($value === true) {
            $this->value = 'TRUE';
        } else if ($value === false) {
            $this->value = 'FALSE';
        } else {
            $this->value = $value;
        }
        $this->valueSet = true;
        return $this;
    }

    /**
     * Get the value of the node.
     */
    public function getValue(): mixed {
        return $this->value;
    }

    /**
     * Get the difference between the node's end and starting offset in the
     * expression from which it was parsed.
     */
    public function getLength(): int {
        $start = $this->data['location']['start']['offset'];
        $end = $this->data['location']['end']['offset'];
        return $end - $start;
    }

    public function __toString(): string
    {
        if ($this->valueSet) {
            if ($this->value === null) {
                return 'NULL';
            }
            if (is_array($this->value)) {
                return implode(', ', $this->value);
            }
            return (string)$this->value;
        }
        throw new Exception;
    }

    /**
     * Determine whether something looks like a node.
     */
    public static function isNode(mixed $candidate): bool {
        return is_array($candidate) && array_key_exists('type', $candidate);
    }

    public function getChildren(): array {
        return $this->getChildrenFromArray($this->data);
    }

    private function getChildrenFromArray(array $arr): array {
        $children = [];
        foreach ($arr as $item) {
            if ($item instanceof self) {
                $children[] = $item;
                continue;
            }
            if (is_array($item)) {
                $children = array_merge($children, $this->getChildrenFromArray($item));
            }
        }
        return $children;
    }

    public function hasChildren(): bool {
        return $this->hasChildrenArray($this->data);
    }

    private function hasChildrenArray(array $data): bool {
        foreach ($data as $item) {
            if ($item instanceof self) {
                return true;
            }
            if (is_array($item)) {
                if ($this->hasChildrenArray($item)) {
                    return true;
                }
            }
        }
        return false;
    }

    /*
     * Implementation of ArrayAccess
     */
    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }
    
    public function offsetGet($offset): mixed {
        return $this->data[$offset];
    }
    
    public function offsetSet($offset, $value): void {
        $this->data[$offset] = $value;
    }
    
    public function offsetUnset($offset): void {
        unset($this->data[$offset]);
    }
}
