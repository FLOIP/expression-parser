<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;

interface Logical extends EvaluatesMethods
{
    /**
     * Returns TRUE if and only if all its arguments evaluate to TRUE
     */
    public function _and(): bool;
    
    /**
     * Returns one value if the condition evaluates to TRUE, and another value if it evaluates to FALSE
     */
    public function _if(): mixed;

    /**
     * Returns TRUE if any argument is TRUE
     */
    public function _or(): bool;
}
