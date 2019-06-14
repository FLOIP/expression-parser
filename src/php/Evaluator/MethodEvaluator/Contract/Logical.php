<?php

namespace Floip\Evaluator\MethodEvaluator\Contract;

interface Logical extends EvaluatesMethods
{
    /**
     * Returns TRUE if and only if all its arguments evaluate to TRUE
     *
     * @param array $args
     * @return bool
     */
    public function and(array $args);

    /**
     * Returns one value if the condition evaluates to TRUE, and another value if it evaluates to FALSE
     *
     * @param array $args
     * @return bool
     */
    public function if(array $args);

    /**
     * Returns TRUE if any argument is TRUE
     *
     * @param array $args
     * @return bool
     */
    public function or(array $args);
}
