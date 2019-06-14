<?php

namespace Viamo\Floip\Evaluator\MethodEvaluator;

use Viamo\Floip\Evaluator\MethodEvaluator\Contract\EvaluatesMethods;

abstract class AbstractMethodHandler implements EvaluatesMethods
{
    public function handles()
    {
        $exclude = ['handles'];
        $methods = \get_class_methods(static::class);
        return array_diff($methods, $exclude);
    }
}
