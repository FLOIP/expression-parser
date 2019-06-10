<?php

namespace Floip\ParseTree;

/**
 * Parse Tree Node
 * @property-read string $type
 * @property-read Location $location
 * @property-read array $args
 */
class Node
{
    const ACCESS = 'ACCESS';
    const FUNCTION = 'FUNCTION';
    
    private $type = '';
    private $location = null;
    private $args = [];

    public function __construct($type, Location $location, array $args = [])
    {
        $this->type = $type;
        $this->location = $location;
        $this->args = $args;
    }

    public function __get($prop)
    {
        return $this->$prop;
    }
}
