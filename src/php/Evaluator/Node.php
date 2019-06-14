<?php

namespace Floip\Evaluator;

class Node implements \ArrayAccess
{
    private $data = [];
    private $value = null;
    private $valueSet = false;

    public function __construct($data)
    {
        $this->data = array_map([$this, 'transformData'], $data);
    }

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

    public function setValue($value)
    {
        $this->value = $value;
        $this->valueSet = true;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

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

    public static function isNode($candidate)
    {
        return \is_array($candidate) && \key_exists('type', $candidate);
    }

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
