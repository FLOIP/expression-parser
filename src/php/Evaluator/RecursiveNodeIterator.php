<?php

namespace Viamo\Floip\Evaluator;

use RecursiveArrayIterator;

class RecursiveNodeIterator extends RecursiveArrayIterator {
    
    /**
     * @inheritdoc
     */
    public function hasChildren(): bool {
        $current = $this->current();
        if ($current instanceof Node) {
            return $this->current()->hasChildren();
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getChildren(): RecursiveArrayIterator|RecursiveNodeIterator|null {
        return new RecursiveNodeIterator($this->current()->getChildren());
    }
}
