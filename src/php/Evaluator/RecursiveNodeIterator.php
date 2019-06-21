<?php

namespace Viamo\Floip\Evaluator;

class RecursiveNodeIterator extends \RecursiveArrayIterator
{
    /**
     * @inheritdoc
     */
    public function hasChildren()
    {
        $current = $this->current();
        if ($current instanceof Node) {
            return $this->current()->hasChildren();
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getChildren()
    {
        return new RecursiveNodeIterator($this->current()->getChildren());
    }
}
