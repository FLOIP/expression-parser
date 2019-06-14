<?php

namespace Floip\Evaluator\MethodEvaluator\Contract;

interface Logical extends EvaluatesMethods
{
    /**
     * Returns TRUE if and only if all its arguments evaluate to TRUE
     *
     * @param mixed ...$args
     * @return bool
     */
    public function and();

    /**
     * Returns one value if the condition evaluates to TRUE, and another value if it evaluates to FALSE
     *
     * @param mixed ...$args
     * @return bool
     */
    public function if();

    /**
     * Returns TRUE if any argument is TRUE
     *
     * @param mixed ...$args
     * @return bool
     */
    public function or();
}
