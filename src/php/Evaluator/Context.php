<?php

namespace Floip\Evaluator;

class Context
{
    /** @var array $data */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function get($key, $default = '')
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }
}
