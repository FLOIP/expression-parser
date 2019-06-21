<?php

namespace Viamo\Floip\Evaluator;

class Node implements \ArrayAccess
{
    /** @var array */
    private $data = [];

    /** @var mixed */
    private $value = null;

    /** @var bool */
    private $valueSet = false;

    public function __construct($data)
    {
        $this->data = array_map([$this, 'transformData'], $data);
    }

    /**
     * Recurse into a tree and transform node structures into Node objects.
     *
     * @param mixed $data
     * @return mixed
     */
    public function transformData($data)
    {
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
     *
     * @param mixed $value
     * @return Node
     */
    public function setValue($value)
    {
        if ($value === true) {
            $this->value = 'TRUE';
        } elseif ($value === false) {
            $this->value = 'FALSE';
        } else {
            $this->value = $value;
        }
        $this->valueSet = true;
        return $this;
    }

    /**
     * Get the value of the node.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the difference between the node's end and starting offset in the
     * expression from which it was parsed.
     *
     * @return int
     */
    public function getLength()
    {
        $start = $this->data['location']['start']['offset'];
        $end = $this->data['location']['end']['offset'];
        return $end - $start;
    }

    public function __toString()
    {
        if ($this->valueSet) {
            return (string)$this->value;
        }
        throw new \Exception;
    }

    /**
     * Determine whether something looks like a node.
     *
     * @param mixed $candidate
     * @return bool
     */
    public static function isNode($candidate)
    {
        return \is_array($candidate) && \key_exists('type', $candidate);
    }

    public function getChildren()
    {
        return $this->getChildrenFromArray($this->data);
    }

    private function getChildrenFromArray(array $arr)
    {
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

    public function hasChildren()
    {
        return $this->hasChildrenArray($this->data);
    }

    private function hasChildrenArray(array $data)
    {
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
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }
    public function offsetSet($offset , $value)
    {
        $this->data[$offset] = $value;
    }
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}
