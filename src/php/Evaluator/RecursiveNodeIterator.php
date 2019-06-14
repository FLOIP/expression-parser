<?php

namespace Viamo\Floip\Evaluator;

class RecursiveNodeIterator extends \RecursiveArrayIterator
{
    /**
     * @inheritdoc
     */
    public function hasChildren()
    {
        return $this->current()->hasChildren();
    }

    /**
     * @inheritdoc
     */
    public function getChildren()
    {
        return new RecursiveNodeIterator($this->current()->getChildren());
    }
}
