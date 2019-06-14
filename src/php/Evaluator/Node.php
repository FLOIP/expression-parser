<?php

namespace Floip\Evaluator;

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
        $this->value = $value;
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
            return $this->value;
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
