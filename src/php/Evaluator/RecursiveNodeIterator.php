<?php

namespace Floip\Evaluator;

class RecursiveNodeIterator extends \RecursiveArrayIterator
{
    public function hasChildren()
    {
        return $this->current()->hasChildren();
    }

    public function getChildren()
    {
        return new RecursiveNodeIterator($this->current()->getChildren());
    }
}
