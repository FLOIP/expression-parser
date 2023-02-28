<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\EvaluatesMethods;
use Viamo\Floip\Evaluator\Node;

abstract class AbstractMethodHandler implements EvaluatesMethods
{
    public function handles(): array
    {
        $exclude = ['handles'];
        $methods = \get_class_methods(static::class);
        return array_diff($methods, $exclude);
    }

    protected function value($thing)
    {
        if ($thing instanceof Node) {
            return $thing->getValue();
        }
        return $thing;
    }
}
